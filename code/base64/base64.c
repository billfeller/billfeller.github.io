#include <stdio.h>
#include <stdlib.h>
#include <string.h>

/* {{{ base64 tables */
// 00000000 - 00111111(2^6-1)
static const char base64_table[] = {
    'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
    'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
    'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm',
    'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
    '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '+', '/', '\0'
};

static const char base64_pad = '=';

static const short base64_reverse_table[256] = {
    -2, -2, -2, -2, -2, -2, -2, -2, -2, -1, -1, -2, -2, -1, -2, -2,
    -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2,
    -1, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, 62, -2, -2, -2, 63,
    52, 53, 54, 55, 56, 57, 58, 59, 60, 61, -2, -2, -2, -2, -2, -2,
    -2,  0,  1,  2,  3,  4,  5,  6,  7,  8,  9, 10, 11, 12, 13, 14,
    15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, -2, -2, -2, -2, -2,
    -2, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40,
    41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, -2, -2, -2, -2, -2,
    -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2,
    -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2,
    -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2,
    -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2,
    -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2,
    -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2,
    -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2,
    -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2
};
/* }}} */

unsigned char *php_base64_encode(const unsigned char *str, int length) /* {{{ */
{
    const unsigned char *current = str;
    unsigned char *p;
    unsigned char *result;

    if (length < 0) {
        return NULL;
    }

    // 申请内存, 每3个字节转成4个字节 length+2表示向上取整
    result = (unsigned char *) malloc((length + 2) / 3 * 4 * sizeof(char));
    // p指向第一个字节
    p = result;

    // 每3个字节转成4个字节，如 3*8 = 4*6 = 24
    while (length > 2) { /* keep going until we have less than 24 bits */
        *p++ = base64_table[current[0] >> 2]; // 第1个字节 取第1个字节右移2位，去掉低2位，高2位补零
        *p++ = base64_table[((current[0] & 0x03) << 4) + (current[1] >> 4)]; // 第2个字节 取第1个字节高6位去掉(& 00000011(0x03))然后左移4位，第2个字节右移4位，相加即可
        *p++ = base64_table[((current[1] & 0x0f) << 2) + (current[2] >> 6)]; // 第3个字节 第2个字节去掉高4位(& 00001111(0x0f))并左移两位(得高6位)，第3个字节右移6位并去掉高6位(得低2位)，相加即可
        *p++ = base64_table[current[2] & 0x3f]; // 第4个字节 取第3个字节去掉高2位(& 00111111(0x3f))即可

        current += 3; // 指针指向第3*N+1个字节
        length -= 3; /* we just handle 3 octets of data */
    }

    // 处理多余的字节
    /* now deal with the tail end of things */
    if (length != 0) {
        *p++ = base64_table[current[0] >> 2]; // 第1个字节 取第1个字节右移两位，去掉低2位，高2位补零
        if (length > 1) { // 若剩余2个字节 
            *p++ = base64_table[((current[0] & 0x03) << 4) + (current[1] >> 4)];
            *p++ = base64_table[(current[1] & 0x0f) << 2];
            *p++ = base64_pad; // 最后填充1个=
        } else { // 若剩余1个字节 
            *p++ = base64_table[(current[0] & 0x03) << 4]; // 取第1个字节高6位去掉(& 00000011(0x03))然后左移4位
            *p++ = base64_pad; // 最后填充2个=
            *p++ = base64_pad;
        }
    }

    *p = '\0';
    return result;
}
/* }}} */

int main(int argc, char **argv) {
    if (argc < 2) {
        printf("argc need to be larger than 1.\n");
        return -1;
    }

    char *test = argv[1];
    char *test2 = php_base64_encode(test, strlen(test));
    printf("%s\n", test2);
    return 0;
}