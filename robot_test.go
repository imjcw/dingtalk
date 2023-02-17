package dingtalk

import (
	"testing"
)

func TestNewRobot(t *testing.T) {
	NewRobot("", "", ROBOT_SIGNATURE).SendText(RobotText{
		Content: "测试一下",
	})
}
