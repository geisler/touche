//David Crim - spring 2005
//Stefan Brandle - fall 2006

/*
   Usage

   chroot_wrapper OPTIONS PATH COMMAND INPUT OUTPUT

   PATH - the path for the new chroot
   COMMAND - the command to execute within the new chroot (local to the new chroot)
*/
#include <sys/types.h>
#include <unistd.h>
#include <errno.h>
#include <malloc.h>
#include <stdlib.h>
#include <sys/wait.h>
#include <string.h>
#include <sys/mount.h>
#include <stdio.h>
// Configurable values go here
// Ideally, all the "configurables" (uid, gid, judge home, chroot.log) will be parameterized. -sb
const int JUDGE_UID = 5001;
const int JUDGE_GID = 100;
#define JUDGE_HOME  "/home/contest/develop/logs"

int main(int argc, char** argv) {
		FILE* pErrFileC;
		FILE* pErrFileP;
		int x;
		int child_exit_status = 0;
		// Need to change this so have a separate log per program judged. Would also be
		// good to set it so don't have to hand-modify error log location. 
		char *fp;
		char ext[] = ".log";
		char JH[] = JUDGE_HOME;
		char child[] = "Child";
		char parent[] = "Parent";
		fp = rindex(argv[3], '/');
		int chk = strlen(fp);
		int length = strlen(ext) + strlen(JH) + strlen(fp);
		char flec[length+6];
		strcpy(flec, JH);
		strcat(flec, fp);
		strcat(flec, child);
		strcat(flec, ext);
		pErrFileC = fopen(flec, "w");
		char flep[length+7];
		strcpy(flep, JH);
                strcat(flep, fp);
                strcat(flep, parent);
                strcat(flep, ext);
                pErrFileP = fopen(flep, "w");
		//pErrFile = fopen(JUDGE_HOME "/chroot.log", "w");
		if(!pErrFileP || !pErrFileC) {
			printf("Failed to open log for writing!\n");
		}
		fprintf(pErrFileC, "chroot_wrapper.exe log file\n");
		fprintf(pErrFileP, "chroot_wrapper.exe log file\n");
		 for(x = 0; x < argc; x++){
			 fprintf(pErrFileC, "Arg #%d: %s\n", x, argv[x]);
                         fprintf(pErrFileP, "Arg #%d: %s\n", x, argv[x]);
		 }

		if(argc != 6)
		{
				 printf("Too few arguments\n");
				 printf("Got %d, expected 5 arguments\n", argc);
				 printf("Usage\n");
				 printf("chroot_wrapper OPTIONS PATH COMMAND INPUT OUTPUT\n\n");
				 printf("OPTIONS - 0 if not using /proc filesystem, 1 if you are using it\n");
				 printf("PATH - the path for the new chroot\n");
				 printf("COMMAND - the command to execute within the new chroot");
				 printf(" (local to the new chroot)\n");
				 printf("INPUT - the inputfile (stdin will be redirected from here)\n");
				 printf("OUTPUT - the output file (stdout will be redirected here)\n");
				 fprintf(pErrFileC, "Not enough arugements\n");
                                 fprintf(pErrFileP, "Not enough arugements\n");
				 fclose(pErrFileP);
                                 fclose(pErrFileC);
				 return 0;
		}

		
		int PROC = *argv[1];
		PROC -= '0'; //to convert from character to actual integer value
		fprintf(pErrFileP, "Swap:%d\n", PROC);
		fprintf(pErrFileP, "Running command: %s\n", argv[3]);
		fflush(pErrFileP);
		fprintf(pErrFileC, "Swap:%d\n", PROC);
                fprintf(pErrFileC, "Running command: %s\n", argv[3]);
                fflush(pErrFileC);

		//hardcoded values to use
		//these are the gid and uid of the user that will be dropped to when chroot'ed
		uid_t uid = JUDGE_UID;
		gid_t gid = JUDGE_GID;
		
		int result;

		if(strlen(argv[2]+6) > 100)
		{
			fprintf(pErrFileP, "Your path variable is too large, this might be an attempt to comprimise the script\n");
			fprintf(pErrFileP, "Exiting program, please shorten your path variable (use synlinks if you need to)\n");
			fprintf(pErrFileC, "Your path variable is too large, this might be an attempt to comprimise the script\n");
                        fprintf(pErrFileC, "Exiting program, please shorten your path variable (use synlinks if you need to)\n");
			return -1;
		}
		char* mountpoint = (char*)malloc(strlen(argv[2])+6);
		if(!mountpoint)
		{
			fprintf(pErrFileP, "The malloc call failed, this is a fatal error, exiting....\n");
                        fprintf(pErrFileC, "The malloc call failed, this is a fatal error, exiting....\n");
			return -1;
		}
		if(PROC == 1){
				strcpy(mountpoint, argv[2]);
				strcat(mountpoint, "/proc");
				result = mount("/proc", mountpoint, "proc", 0x0, NULL);
				if(result == -1){
					char* msg;
					msg = (char*)strerror(errno);
					fprintf(pErrFileP, "An error has occured during the mount call:\
									%d\n%s\n", errno, msg);
					fprintf(pErrFileC, "An error has occured during the mount call:\
                                                                        %d\n%s\n", errno, msg);
					
					return -1;
				}
		}

		pid_t pid;
		/*pid = fork();
		fprintf(pErrFile, "pid = %d\n", pid);*/
		if((pid=fork()) == 0)
		{
		/*fprintf(pErrFile, "Entered the child loop\n");*/

				char *exec_argv = (char*)malloc(sizeof(char) * strlen(argv[3])+1);
				char **execv = (char**)malloc(sizeof(char)*10);
				int ctr = 0;
				char *delim = " ";
				char* ptr;

				result = chroot(argv[2]);
				if(result == -1){
					char* msg;
					msg = (char*)strerror(errno);
					fprintf(pErrFileC, "Child: An error has occured during the chroot call:\
									%d\n%s\n", errno, msg);
					
					return -1;
				}

				result = chdir("/");
				if(result == -1){
					char* msg;
					msg = (char*)strerror(errno);
					fprintf(pErrFileC, "Child: An error has occured during the chdir call:\
									%d\n%s\n", errno, msg);
					
					return -1;
				}
					
				fprintf(pErrFileC, "Child: Successful chroot\n");
				
				
				result = setresgid(gid, gid, gid);
				if(result == -1){
					char* msg;
					msg = (char*)strerror(errno);
					fprintf(pErrFileC, "Child: An error has occured during the setresgid \
									call: %d\n%s\n", errno, msg);
					fprintf(pErrFileC, "This probably means that the process was \
									not privilaged enough to make this call\n");
					return -1;
				}
				result = setresuid(uid, uid, uid);
				if(result == -1){
					char* msg;
					msg = (char*)strerror(errno);
					fprintf(pErrFileC, "Child: An error has occured during the \
									setresuid call: %d\n%s\n", errno, msg);
					fprintf(pErrFileC, "This probably means that the process \
									was not privilaged enough to make this call\n");
					return -1;
				}
				
				fprintf(pErrFileC, "Child: Uid:%d\nEUid:%d\n", getuid(), geteuid());
				fprintf(pErrFileC, "Child: Sucessful setresuid\n");


				fprintf(pErrFileC, "Child: Sucessful setresgid\n");
				

				//result = system(argv[3]);

				//make an exec call

				//close stdin, std out
				//reopen then with the new filenames

				if(freopen(argv[4], "r", stdin) == NULL){
					fprintf(pErrFileC, "Child: Could not reassociate stdin with test input data\n");
					return -1;
				}
				//LS - trying to pipe everything through head to limit file size
				pid_t pidhead;
				int pipes[2];
				
				pipe(pipes);
				pidhead = fork();
				//if the process is going to be the head
				if(pidhead == 0)
				{
					//redirects standard out of head to a file
					if(freopen(argv[5], "w", stdout) == NULL){
						fprintf(pErrFileC, "Child: Could not reassociate stdout of 'head' in \
									output filter.\n");
						return -1;
					}
					//redirects standard in of head to output of process
					close(pipes[1]);
					close(0);
					dup(pipes[0]);
					close(pipes[0]);

					//exec head and make the cap on it 1 million characters
					execlp("head", "head", "-c", "1000000", NULL);
					//Repeat after me: never assume that exec worked! Added below. -sb
					fprintf(pErrFileC, "Child: could not execute 'head'.\n");
					return -1;
				}
				//if the process is going to be the code
				else
				{
					close(pipes[0]);
					close(1);
					dup(pipes[1]);
					close(pipes[1]);
				}
				//LS - done editing
				
				strcpy(exec_argv, argv[3]);
				execv[ctr++] = strtok(exec_argv, delim);
				fprintf(pErrFileC, "First Arg: %s\n", execv[0]);
				while((ptr=strtok(NULL, delim)) != NULL)
				{
					fprintf(pErrFileC, "Added arg: %s\n", ptr);
					execv[ctr++] = ptr;
				}
				execv[ctr] = NULL;
					
				fflush(pErrFileC);
				execvp(execv[0], execv);
				
				//execvp function should not return
				//it would only return on an error...

				char* msg;
				msg = (char*)strerror(errno);
				fprintf(pErrFileC, "Child: An error has occured during the \
					execvp call: %d\n%s\n", errno, msg);
				return -1;
		}
		else
		{
				
				while(waitpid(pid, &child_exit_status, 0) != pid)
				{
				}
				fprintf(pErrFileP, "Parent: Child exit status%d\n", child_exit_status);
				fprintf(pErrFileP, "Parent: Done waiting\n");
				if(PROC){
						umount(mountpoint);
						fprintf(pErrFileP, "Parent: Umount\n");
				}
		}
		return WEXITSTATUS(child_exit_status);
}
