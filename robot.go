package dingtalk

import (
	"bytes"
	"crypto/hmac"
	"crypto/sha256"
	"encoding/base64"
	"encoding/json"
	"fmt"
	"net/http"
	"net/url"
	"time"

	"github.com/imjcw/httpgo"
)

const (
	_ = iota
	ROBOT_KEYWORD
	ROBOT_SIGNATURE
)

var httpClient = &httpgo.Client{
	BaseUrl: "https://oapi.dingtalk.com",
	Timeout: 5 * time.Second,
}

type robot struct {
	accessToken    string
	accessSecret   string
	validateMethod int
}

// NewRobot robot instance
func NewRobot(accessToken string, accessSecret string, validateMethod int) robot {
	return robot{
		accessToken:    accessToken,
		accessSecret:   accessSecret,
		validateMethod: validateMethod,
	}
}

// SendText 发送文本消息
func (r robot) SendText(t RobotText) error {
	content, err := r.parseMessageBody(t)
	if err != nil {
		return err
	}
	return r.send(content)
}

// SendLink 发送链接消息
func (r robot) SendLink(l RobotLink) error {
	content, err := r.parseMessageBody(l)
	if err != nil {
		return err
	}
	return r.send(content)
}

// SendMarkdown 发送markdown消息
func (r robot) SendMarkdown(m RobotMarkdown) error {
	content, err := r.parseMessageBody(m)
	if err != nil {
		return err
	}
	return r.send(content)
}

// SendActionCard 发送ActionCard消息
func (r robot) SendActionCard(a RobotActionCard) error {
	content, err := r.parseMessageBody(a)
	if err != nil {
		return err
	}
	return r.send(content)
}

// SendActionCardButton 发送ActionCardButton消息
func (r robot) SendActionCardButton(a RobotActionCardButton) error {
	content, err := r.parseMessageBody(a)
	if err != nil {
		return err
	}
	return r.send(content)
}

// SendFeedCard 发送FeedCard消息
func (r robot) SendFeedCard(f RobotFeedCard) error {
	content, err := r.parseMessageBody(f)
	if err != nil {
		return err
	}
	return r.send(content)
}

func (r robot) send(c []byte) error {
	req := &httpgo.Request{
		Method: http.MethodPost,
		Uri:    "robot/send",
		Query: map[string]string{
			"access_token": r.accessToken,
		},
		Header: http.Header{
			"Content-Type": []string{httpgo.ContentJson},
		},
		Body: bytes.NewReader(c),
	}
	if r.validateMethod == ROBOT_SIGNATURE {
		timestamp := fmt.Sprintf("%v", time.Now().UnixMilli())
		h := hmac.New(sha256.New, []byte(r.accessSecret))
		h.Write([]byte(timestamp + "\n" + r.accessSecret))
		req.Query["sign"] = url.QueryEscape(base64.StdEncoding.EncodeToString(h.Sum(nil)))
		req.Query["timestamp"] = timestamp
	}
	_, err := httpClient.Do(req)
	return err
}

func (r robot) parseMessageBody(t interface{}) ([]byte, error) {
	msgtype := ""
	content := map[string]interface{}{}
	at := map[string]interface{}{}
	switch ty := t.(type) {
	case RobotText:
		msgtype = "text"
		content["content"] = ty.Content
		if len(ty.AT.AtMobiles) > 0 {
			at["atMobiles"] = ty.AT.AtMobiles
		}
		if len(ty.AT.AtUserIds) > 0 {
			at["atUserIds"] = ty.AT.AtUserIds
		}
		at["isAtAll"] = ty.AT.IsAtAll
	case RobotLink:
		msgtype = "link"
		content["title"] = ty.Title
		content["text"] = ty.Text
		content["picUrl"] = ty.PicUrl
		content["messageUrl"] = ty.MessageUrl
	case RobotMarkdown:
		msgtype = "markdown"
		content["title"] = ty.Title
		content["text"] = ty.Text
		if len(ty.AT.AtMobiles) > 0 {
			at["atMobiles"] = ty.AT.AtMobiles
		}
		if len(ty.AT.AtUserIds) > 0 {
			at["atUserIds"] = ty.AT.AtUserIds
		}
		at["isAtAll"] = ty.AT.IsAtAll
	case RobotActionCard:
		msgtype = "actionCard"
		content["title"] = ty.Title
		content["text"] = ty.Text
		content["singleTitle"] = ty.SingelTitle
		content["singleURL"] = ty.SingelUrl
	case RobotActionCardButton:
		msgtype = "actionCard"
		content["title"] = ty.Title
		content["text"] = ty.Text
		content["btnOrientation"] = 0
		if ty.BtnHorizontal {
			content["btnOrientation"] = 1
		}
		btns := []map[string]string{}
		for _, btn := range ty.Buttons {
			btns = append(btns, map[string]string{
				"title":     btn.Title,
				"actionURL": btn.Url,
			})
		}
		content["btns"] = btns
	case RobotFeedCard:
		msgtype = "feedCard"
		links := []map[string]string{}
		for _, link := range ty.Links {
			links = append(links, map[string]string{
				"title":      link.Title,
				"messageURL": link.MessageURL,
				"picURL":     link.PicURL,
			})
		}
		content["links"] = links
	default:
		return nil, fmt.Errorf("Invalid Type: %v", ty)
	}
	msg := map[string]interface{}{
		"msgtype": msgtype,
	}
	msg[msgtype] = content
	if len(at) > 0 {
		msg["at"] = at
	}
	return json.Marshal(msg)
}
