## OmpTG Online Version

**Environment Required**

- [OmpTG](https://github.com/Kingtous/OmpTG)
  - place into `omptg-tools/OmpTG`

- [OMPi_Cleaner](https://github.com/Kingtous/OMPi_Cleaner)
  - place into `omptg-tools/trimming`

- [ALFBackend](https://github.com/visq/ALF-llvm)
  - compile and place into `omptg-tools/ALFBackend`

- [SWEET](http://www.es.mdh.se/publications/3693-SWEET_____a_Tool_for_WCET_Flow_Analysis)
  - place binary file `sweet` into `omptg-tools` and `/usr/bin`
- Graphviz
  - `apt install graphviz`
- Python3
  - Networkx **2.2**
  - Pydot
  - Graphviz

---

### Deploy

#### 1. Deploy Manually

- Setup environments above
- Extra environments
  - `apache` or `nginx`
  - `php 7.1+`
    - allow `proc_open` in php config.

#### 2. Deploy OmpTG Online By Docker

- `Pull Image`

> Download From Aliyun Server in Hangzhou

```shell
docker pull registry.cn-hangzhou.aliyuncs.com/omptg/omptg-server:v1
```

- `Run Image`

```shell
docker run --name omptg-online -dit -P -p 8000:80 -p 8888:8888 registry.cn-hangzhou.aliyuncs.com/omptg/omptg-server:v1 /bin/bash start.sh
```

- `Open OmpTG Online in Browser`
  - `127.0.0.1:8000`

- `Open BT-Panel in Browser`
  - `127.0.0.1:8888/xxx`
    - get `xxx` from terminal, type `/etc/init.d/bt default`