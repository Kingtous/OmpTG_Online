import re as r


bound_regex = r'[^:]+:[\d]+'
NodeReg=r'".*?"\s*\[.*?\]'
NodeReg_Label=r'label=".*?"'
NodeReg_Head=r'^".*?"'
EdgeReg=r'".*?"\s*->\s*".*?"'
subgraphNameReg=r'".*?"'


def preprocess(Path):
    '''
    :param Path: Preprocessing DOT file generated by SWEET
    :return: Dot Data that suit networkx
    '''
    Input=open(Path,'r')
    Output=open(Path+'_pd','w')
    Output_Declaration = open(Path + '_dec', 'w')
    Definition=''

    #step1 生成对应label转换表,查找definition
    DefinitionDict={}
    try:
        lineData=Input.readlines()
        for line in lineData:
            if line.strip()!='':
                line=line.strip()
                if r.match(NodeReg,line) or r.match(EdgeReg,line):
                    if r.match(NodeReg,line):
                        #生成表
                        head=r.search(NodeReg_Head,line).group()
                        label=r.search(NodeReg_Label,line).group().split('=')[-1]
                        if label=='""':
                            label=head
                        DefinitionDict[head]=label
                        #对entry_exit做处理
                        line=line.replace('""',head)
                    Definition=Definition+line+'\n'

                else:
                    continue
            else:
                continue

        #step 2 修改definition

        for key in DefinitionDict.keys():
            if DefinitionDict[key]!='""':
                # entry exit 没有label名字
                Definition=Definition.replace(key,DefinitionDict[key])
            else:
                continue

        #step 3 子图中保留结点，生成声明信息
        Input.seek(0)
        # flag -> True , put Definition
        Flag=False
        for line in lineData:
            if line.strip().startswith('subgraph') and Flag==False:
                Flag=True
                Output.write(Definition)
            if line.strip()!='':
                line=line.strip()
                if r.match(NodeReg,line) or r.match(EdgeReg,line):
                    #留结点声明,改label,label为空的就不换了
                    if r.match(NodeReg,line):
                        label=r.search(NodeReg_Label,line)
                        label_name=label.group().split('=')[-1]
                        if label_name!='""':
                            Output.write(label_name+'\n')
                            if Flag==True:
                                Output_Declaration.write(label_name+'\n')
                        else:
                            Output.write(r.search(NodeReg_Head,line).group()+'\n')
                            if Flag == True:
                                Output_Declaration.write(r.search(NodeReg_Head,line).group()+'\n')
                    pass
                else:
                    if line.startswith('subgraph'):
                        # 给subgraph加上'cluster'_
                        text=r.search(subgraphNameReg,line).group().strip('"')
                        line=r.sub(subgraphNameReg,'"cluster_'+text+'"',line)
                        Output.write(line+'\n'+'label='+text+'\nstyle="bold"\n')

                        if Flag == True:
                            Output_Declaration.write(line+'\n'+'label='+text+'\nstyle="bold"\n')
                        if text.startswith('_thrFunc'):
                            Output.write('\ncolor="blue"\n')
                            if Flag ==True:
                                Output_Declaration.write('color="blue"\n')


                    else:
                        Output.write(line+'\n')
                        if Flag == True:
                            Output_Declaration.write(line+'\n')
            else:
                continue

    except:
        print('Preprocessing Error.')
        exit(-1)
    finally:
        Input.close()
        Output.close()
        Output_Declaration.close()