#include <unistd.h>

int main(){
    return unlink("/tmp/canari");
}

