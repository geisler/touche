# LaTeX2HTML 2002-2-1 (1.71)
# Associate images original text with physical files.


$key = q/{lstlisting}slashslashshouldIeatwithaforkorspoon?{lstlisting};AAT/;
$cached_env_img{$key} = q|<IMG
 WIDTH="350" HEIGHT="16" ALIGN="BOTTOM" BORDER="0"
 SRC="|."$dir".q|img1.png"
 ALT="\begin{lstlisting}
// should I eat with a fork or spoon?
\end{lstlisting}">|; 

$key = q/{lstlisting}execute(){lstlisting};AAT/;
$cached_env_img{$key} = q|<IMG
 WIDTH="80" HEIGHT="16" ALIGN="BOTTOM" BORDER="0"
 SRC="|."$dir".q|img2.png"
 ALT="\begin{lstlisting}
execute()
\end{lstlisting}">|; 

1;

