package dingtalk

// RobotText 文本类型
type RobotText struct {
	Content string
	AT
}

// RobotLink 链接类型
type RobotLink struct {
	Title      string
	Text       string
	PicUrl     string
	MessageUrl string
}

// RobotMarkdown Markdown类型
type RobotMarkdown struct {
	Title string
	Text  string
	AT
}

// RobotActionCard ActionCard类型
type RobotActionCard struct {
	Title       string
	Text        string
	SingelTitle string
	SingelUrl   string
}

// RobotActionCardButton ActionCard类型
type RobotActionCardButton struct {
	Title         string
	Text          string
	BtnHorizontal bool
	Buttons       []ActionCardButton
}

// RobotFeedCard FeedCard类型
type RobotFeedCard struct {
	Links []FeedCardLink
}

// ActionCardButton ActionCard按钮
type ActionCardButton struct {
	Title string
	Url   string
}

// FeedCardLink FeedCardLink
type FeedCardLink struct {
	Title      string
	MessageURL string
	PicURL     string
}

// AT at
type AT struct {
	AtMobiles []string
	AtUserIds []string
	IsAtAll   bool
}
