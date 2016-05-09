#include <stdio.h>
#include <time.h>
#include <sys/time.h>

/* Return the UNIX time in microseconds */
long long ustime(void) {
    struct timeval tv;
    long long ust;

    gettimeofday(&tv, NULL);
    ust = ((long long)tv.tv_sec)*1000000;
    ust += tv.tv_usec;
    return ust;
}

/* Return the UNIX time in milliseconds */
long long mstime(void) {
    return ustime()/1000;
}

int main() {
    printf("microseconds: %lld\n", ustime());
    printf("milliseconds: %lld\n", mstime());
    return 0;
}
