strace -o /tmp/curl.7.19.7.ok.log curl "http://www.desidime.com"
strace -o /tmp/curl.7.19.7.fail.log curl "http://www.desidime.com"
strace -o /tmp/curl.ipv6.resolve.host.fail.log curl -6 http://does.not.exist.foo.