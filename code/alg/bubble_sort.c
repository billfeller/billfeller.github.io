#include <stdio.h>

/**
 * 冒泡排序
 */

int main() {
    int a[] = {-2, 10, 8, 3, 15, 28, 11, 19, 6, 21, 88, 23, 33, -1, 10, 0, 10, 10},
        len = sizeof(a) / sizeof(a[0]),
        i, 
        j;

    for (i = 0; i < len; i ++) {
        for (j = i; j < len - 1; j ++) {
            // 将j,j+1中较大的值与第i对比，取较小的值存储到第i,保证第i个元素保存第i小的元素
            if (a[j] < a[j + 1] && a[j] < a[i]) {
                a[i] = a[i] + a[j];
                a[j] = a[i] - a[j];
                a[i] = a[i] - a[j];
            } else if (a[j] > a[j + 1] && a[i] > a[j + 1]) {
                a[i] = a[i] + a[j + 1];
                a[j + 1] = a[i] - a[j + 1];
                a[i] = a[i] - a[j + 1];
            }
        }
    }

    for (i = 0; i < len; i ++) {
        printf("%d ", a[i]);
    }

    return 0;
}