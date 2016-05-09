#include <stdio.h>

int main() {
    int a[] = {10, 2, 3, 5, 8, 11, 25, 22, 0, -1, 0, -1, 223, 113},
        len = sizeof(a) / sizeof(int),
        i,
        j,
        tmp;

    for (i = 1; i < len; i ++) {
        for (j = i - 1; j >= 0; j --) {
            if (a[j] >= a[j + 1]) {
                a[j] = a[j] + a[j + 1];
                a[j + 1] = a[j] - a[j + 1];
                a[j] = a[j] - a[j + 1];
            }
        }
    }

    for (i = 0; i < len; i ++) {
        printf("%d ", a[i]);
    }

    return 0;
}