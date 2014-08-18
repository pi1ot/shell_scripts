###cgitest.sh
- CGI接口测试工具，遍历指定域名的所有IP（使用nslookup）地址
- Usage: cgitest.sh {url}

###svncl.sh
- SVN修改对比工具，不需要指定具体版本号数字，按照最近第n次修改方式对比，使用系统默认diff工具输出
- Usage: svncl.sh {file} {revstep}

###svndump.sh
- SVN修改对比工具，不需要指定具体版本号数字，按照最近第n次修改方式对比，使用diff2html输出为HTML文件
- Usage: svndump.sh {file} {revstep}

###gbk2utf8.sh
- 自动转换GBK编码文件为UTF8编码，用于封装cat，more，less等工具
- Usage: gbk2utf8.sh {cat|more|less} {file}

###upgrade.php
- MySQL数据表升级脚本，支持逐个字段定制升级转换规则