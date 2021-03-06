//David Crim - spring 2005
//Stefan Brandle - fall 2006

/*
   Usage

   chroot_wrapper OPTIONS PATH COMMAND INPUT OUTPUT LOG_ID

   PATH - the path for the new chroot
   COMMAND - the command to execute within the new chroot (local to the new chroot)
*/

// for chroot()
#define _DEFAULT_SOURCE
// for setresuid() and setresgid(), and asprintf()
#define _GNU_SOURCE

#include <sys/types.h>
#include <unistd.h>
#include <errno.h>
#include <malloc.h>
#include <stdlib.h>
#include <sys/wait.h>
#include <string.h>
#include <sys/mount.h>
#include <stdio.h>
#include <signal.h>
#include <stdarg.h>
#include <sys/resource.h>

// Configurable values go here
// Ideally, all the "configurables" (uid, gid, judge home) will be parameterized. -sb
const int JUDGE_UID = 5001;
const int JUDGE_GID = 100;
const char *JUDGE_HOME = "/home/contest/develop/logs/";

const int MAX_ARGS = 10;

FILE* pErrFileC;
FILE* pErrFileP;

void check_argc(int argc) {
	if(argc != 9) {
		printf("Not right number of arguments\n");
		printf("Got %d, expected 9 arguments\n", argc);
		printf("Usage\n");
		printf("chroot_wrapper OPTIONS PATH COMMAND INPUT OUTPUT ERR_OUT LOG_ID MAX_OUTPUT_SIZE\n\n");
		printf("OPTIONS - 0 if not using a filesystem, 1 if you are using /proc, 2 if you are using /dev/urandom\n");
		printf("PATH - the path for the new chroot\n");
		printf("COMMAND - the command to execute within the new chroot");
		printf(" (local to the new chroot)\n");
		printf("INPUT - the input file (stdin will be redirected from here)\n");
		printf("OUTPUT - the output file (stdout will be redirected here)\n");
		printf("ERR_OUT - the error file (stderr will be redirected here)\n");
		printf("LOG_ID - the unique identifier for the log file\n");
		printf("MAX_OUTPUT_SIZE - the most number of bytes to accept as output\n");

		exit(0);
	}
}

void close_log_files() {
	fclose(pErrFileP);
	fclose(pErrFileC);
}

void open_log_files(const char *id) {
	char ext[] = ".log";
	int length = strlen(ext) + strlen(JUDGE_HOME) + strlen(id);

	char flec[length+6];
	strcpy(flec, JUDGE_HOME);
	strcat(flec, id);
	strcat(flec, "Child");
	strcat(flec, ext);
	pErrFileC = fopen(flec, "w");

	char flep[length+7];
	strcpy(flep, JUDGE_HOME);
	strcat(flep, id);
	strcat(flep, "Parent");
	strcat(flep, ext);
	pErrFileP = fopen(flep, "w");

	if(!pErrFileP || !pErrFileC) {
		printf("Failed to open log for writing!\n");
	}

	atexit(close_log_files);
}

void vlog_out(FILE *file, const char *format, va_list args) {
	vfprintf(file, format, args);
	fflush(file);
}

void child_log(const char *format, ...) {
	va_list args;
	va_start(args, format);
	vlog_out(pErrFileC, format, args);
	va_end(args);
}

void parent_log(const char *format, ...) {
	va_list args;
	va_start(args, format);
	vlog_out(pErrFileP, format, args);
	va_end(args);
}

void both_logs(const char *format, ...) {
	va_list child_args, parent_args;
	va_start(child_args, format);
	va_copy(parent_args, child_args);

	vlog_out(pErrFileC, format, child_args);
	va_end(child_args);

	vlog_out(pErrFileP, format, parent_args);
	va_end(parent_args);
}

int determine_options(const char *options_arg) {
	int rc = options_arg[0] - '0';
	both_logs("Options: %d\n", rc);

	return rc;
}

void check_path_length(const char *path) {
	if(strlen(path)+6 > 100)
	{
		both_logs("Your path variable is too large, this might be an attempt to comprimise the script\n");
		both_logs("Exiting program, please shorten your path variable (use synlinks if you need to)\n");
		exit(-1);
	}
}

void mount_proc(const char *mountpoint) {
	int result = mount("/proc", mountpoint, "proc", MS_BIND, NULL);
	if(result == -1){
		char* msg = (char*)strerror(errno);
		both_logs("An error has occured during the mount call:\
						%d\n%s\n", errno, msg);
		exit(-1);
	}
}

void mount_urandom(const char *mountpoint) {
	int result = mount("/dev/urandom", mountpoint, "devtmpfs", MS_BIND, NULL);
	if (result == -1) {
		char *msg = (char *)strerror(errno);
		both_logs("An error has occured during the mount call:\
						%d\n%s\n", errno, msg);
		exit(-1);
	}
}

void do_chroot(const char *root) {
	int result = chroot(root);
	if(result == -1) {
		char* msg = (char*)strerror(errno);
		child_log("Child: An error has occured during the chroot call:\
						%d\n%s\n", errno, msg);
		
		exit(-1);
	}
}

void do_chdir(const char *dir) {
	int result = chdir(dir);
	if(result == -1) {
		char* msg = (char*)strerror(errno);
		child_log("Child: An error has occured during the chdir call:\
						%d\n%s\n", errno, msg);
		
		exit(-1);
	}
}

void do_setresgid(int gid) {
	int result = setresgid(gid, gid, gid);
	if(result == -1) {
		char* msg = (char*)strerror(errno);
		child_log("Child: An error has occured during the setresgid \
						call: %d\n%s\n", errno, msg);
		child_log("This probably means that the process was \
				not privilaged enough to make this call\n");
		exit(-1);
	}

	child_log("Child: Sucessful setresgid\n");
}

void do_setresuid(int uid) {
	int result = setresuid(uid, uid, uid);
	if(result == -1) {
		char* msg = (char*)strerror(errno);
		child_log("Child: An error has occured during the \
				setresuid call: %d\n%s\n", errno, msg);
		child_log("This probably means that the process \
				was not privilaged enough to make this call\n");
		exit(-1);
	}

	child_log("Child: Uid:%d\nEUid:%d\n", getuid(), geteuid());
	child_log("Child: Sucessful setresuid\n");
}

void reassociate_file(const char *path, const char *mode, FILE *file) {
	if(freopen(path, mode, file) == NULL) {
		child_log("Child: Could not reassociate file to %s\n", path);
		exit(-1);
	}
}

void reassociate_input_and_limited_output(const char *input, const char *output,
					  const char *err_out, int limit)
{
	reassociate_file(input, "r", stdin);
	reassociate_file(output, "w", stdout);
	reassociate_file(err_out, "w", stderr);

	struct rlimit file_limit;
	file_limit.rlim_cur = file_limit.rlim_max = limit;
	int result = setrlimit(RLIMIT_FSIZE, &file_limit);
	if (result == -1) {
		child_log("Child: could not limit file size of output.\n");
		exit(-1);
	}
}

char *create_command_copy(const char *command) {
	char *new_command = (char *)malloc(strlen(command) + 1);
	if (!new_command) {
		child_log("Child: Unable to allocate space for command: %s\n", command);
		exit(-1);
	}

	strcpy(new_command, command);
}

char *setup_proc(const char *path) {
	check_path_length(path);

	char *mountpoint = NULL;
	if (asprintf(&mountpoint, "%s/proc", path) == -1) {
		both_logs("The asprintf() call failed, this is a fatal error, exiting....\n");
		exit(-1);
	}
	mount_proc(mountpoint);

	return mountpoint;
}

char *setup_urandom(const char *path) {
	check_path_length(path);

	char *mountpoint = NULL;
	if (asprintf(&mountpoint, "%s/dev/urandom", path) == -1) {
		both_logs("The asprintf() call failed, this is a fatal error, exiting....\n");
		exit(-1);
	}
	mount_urandom(mountpoint);

	return mountpoint;
}

char **create_execv_from_command(char *command) {
	char **execv = (char **)malloc(sizeof(char *) * MAX_ARGS);

	int ctr = 0;
	char *delim = " ";
	char* ptr;

	execv[ctr++] = strtok(command, delim);
	child_log("First Arg: %s\n", execv[0]);

	while((ptr=strtok(NULL, delim)) != NULL)
	{
		child_log("Added arg: %s\n", ptr);
		execv[ctr++] = ptr;
		if (ctr >= MAX_ARGS - 1) {
			child_log("Child: Too many arguments to handle!");
			exit(-1);
		}
	}
	execv[ctr] = NULL;

	return execv;
}

void execute_command(const char *path, const char *command, const char *input,
		     const char *output, const char *err_out,
		     const char *max_output_size)
{
	do_chroot(path);
	do_chdir("/");
	child_log("Child: Successful chroot\n");
	
	do_setresgid(JUDGE_GID);
	do_setresuid(JUDGE_UID);

	reassociate_input_and_limited_output(input, output, err_out, atoi(max_output_size));
			
	char *exec_argv = create_command_copy(command);
	char **execv = create_execv_from_command(exec_argv);
	execvp(execv[0], execv);
			
	char* msg = (char*)strerror(errno);
	child_log("Child: An error has occured during the \
		execvp call: %d\n%s\n", errno, msg);
	exit(-1);
}

int wait_for_process(pid_t pid) {
	int child_exit_status;

	while (waitpid(pid, &child_exit_status, 0) != pid) {
	}
	parent_log("Parent: Child exit status%d\n", child_exit_status);
	parent_log("Parent: Done waiting\n");

	return child_exit_status;
}

int main(int argc, char** argv) {
	check_argc(argc);

	open_log_files(argv[7]);
	both_logs("chroot_wrapper.exe log file\n");
	for (int x = 0; x < argc; x++) {
		both_logs("Arg #%d: %s\n", x, argv[x]);
	}

	both_logs("Running command: %s\n", argv[3]);

	char *mountpoint = NULL;

	switch (determine_options(argv[1])) {
	    case 0:
	    default:
		break;

	    case 1:
		mountpoint = setup_proc(argv[2]);
		break;

	    case 2:
		mountpoint = setup_urandom(argv[2]);
		break;
	}

	pid_t pid;
	if ((pid=fork()) == 0) {
		execute_command(argv[2], argv[3], argv[4], argv[5], argv[6], argv[8]);
	} else if (pid > 0) {
		int child_exit_status = wait_for_process(pid);
				
		if(mountpoint){
			umount(mountpoint);
			parent_log("Parent: Umount\n");
		}

		if (WIFEXITED(child_exit_status)) {
			int real_exit_status = WEXITSTATUS(child_exit_status);

			parent_log("Parent: Child exited (%i)\n", real_exit_status);
			exit(real_exit_status);
		} else if (WIFSIGNALED(child_exit_status)) {
			int signal_number = WTERMSIG(child_exit_status);

			parent_log("Parent: Child signalled (%i)\n", signal_number);
			raise(signal_number);
		}
	} else {
		both_logs("Unable to create child process.\n");
		exit(-1);
	}

	/* should never get here, so it is an error! */
	return -1;
};
