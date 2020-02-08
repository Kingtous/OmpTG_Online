import os
import sys
import re as r
import Function_OMPi as Function



if os.path.isfile(sys.argv[1]):
    Function.Trim(sys.argv[1])
else :
    #传入的不是文件就终止程序
	print('It\'s not a file')
