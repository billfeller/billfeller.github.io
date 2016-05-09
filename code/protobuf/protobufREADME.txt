protoc --proto_path=/root/wade/git/billfeller.github.io/code/protobuf/ --cpp_out=/root/wade/git/billfeller.github.io/code/protobuf/cpp/ --java_out=/root/wade/git/billfeller.github.io/code/protobuf/java/ --python_out=/root/wade/git/billfeller.github.io/code/protobuf/python/ /root/wade/git/billfeller.github.io/code/protobuf/CommonMessage.proto

protoc -I=./ --cpp_out=./cpp/ LogonReqMessage.proto

protoc -I=./ --java_out=./java/ LogonReqJavaMessage.proto

protoc -I=./ --python_out=./python/ LogonReqMessage.proto
