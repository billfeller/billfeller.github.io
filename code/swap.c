#include <stdio.h>

int main() {
    int a = 1, b = 2;
    int c;

    // 通过c交换a,b
    printf("%d, %d\n", a, b);
    c = a;
    a = b;
    b = c;
    printf("%d, %d\n", a, b);

    // 不依赖任何中间变量实现交换a,b
    printf("%d, %d\n", a, b);
    a = a + b;
    b = a - b;
    a = a - b;
    printf("%d, %d\n", a, b);

    // 不依赖任何中间变量实现交换a,b
    printf("%d, %d\n", a, b);
    a = a ^ b;
    b = a ^ b;
    a = a ^ b;
    printf("%d, %d\n", a, b);

    return 0;
}
