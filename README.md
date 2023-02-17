## 项目简介

一些关于钉钉的包

## 安装

```bash
go get -u github.com/imjcw/dingtalk 
```

## 使用

```golang
package main

import (
	"fmt"

	"github.com/imjcw/dingtalk"
)

func main() {
    accessToken := ""
    accessSecret := ""
	r := dingtalk.NewRobot(accessToken, accessSecret, dingtalk.ROBOT_SIGNATURE)
	err := r.SendText(dingtalk.RobotText{
		Content: "测试一下",
	})
	fmt.Println(err)
}
```