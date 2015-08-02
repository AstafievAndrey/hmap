/*
    C socket server example, handles multiple clients using threads
*/
 
#include<stdio.h>
#include<string.h>    //strlen
#include<stdlib.h>    //strlen
#include<sys/socket.h>
#include<arpa/inet.h> //inet_addr
#include<unistd.h>    //write
#include<pthread.h> //for threading , link with lpthread

 

 
int main(int argc , char *argv[])
{
	struct pocket {                  
			char mes;       
			unsigned int len;        
			unsigned int s_num;  
			char type;
			char p_num;
			char *parol;
			char *d;
			char crm;
	}; 
	pocket th;
	int i;
	char *s= "DATA ACCEPT=1\r\n";
	th.mes = 1;
	th.len = 234;
	th.s_num = 22;
	th.type = 2;
	th.p_num = 4;
	th.parol = "12345";
	th.d = "as34r";
	th.crm = 1;
	printf("Data: %s\n",th.parol);

	for ( i = 0; i < 15; ++i )
{
   printf("%02X ", s[i]);
}
    return 0;
}