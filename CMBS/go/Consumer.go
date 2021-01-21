// time: 20201208
// URL : http://123.56.234.253:1111/consumer
// 功能：从消息队列中取出json格式的order，并实时处理订单
// 输入：消息队列
// 输出：mysql数据库

package main

import (
	"encoding/json"
	"io"
	"log"
	"net/http"
	"errors"
	"fmt"
	"gorm.io/driver/mysql"
	"gorm.io/gorm"
	"github.com/streadway/amqp"
)
type Order struct {
    StudentID int           `json:"StudentID,int"`
    room int     			`json:"room,int"`
}
//定义一个struct类型和MYSQL表进行绑定或者叫映射，struct字段和MYSQL表字段一一对应
//这样，Building类型就可以代表web2020数据库中的某个表了
type classroom struct {
	//通过后面的标签说明，定义golang字段和mysql表字段的对应关系
	id				int		`gorm:"primaryKey"`
	building   	 	int    	`gorm:"column:building"`
	floor 			int		`gorm:"column:floor"`
	room			int		`gorm:"column:room"`
	seats			int		`gorm:"column:seats"`
	allocated       int    	`gorm:"column:allocated"`
}
type student struct {
	id           	int    	`gorm:"primaryKey"`
	StudentID		int   	`gorm:"column:StudentID"`
	password   	 	string  `gorm:"column:password"`
	room       		int    	`gorm:"column:room"`
}
type user struct {
	id           	int    	`gorm:"primaryKey"`
	username		string  `gorm:"column:username"`
	password		string	`gorm:"column:password"`
	cookie			string	`gorm:"column:cookie"`
	authority		int		`gorm:"column:authority"`
}


func failOnError(err error, msg string) {
  if err != nil {
    log.Fatalf("%s: %s", msg, err)
  }
}

func ReceiveOrder() {
    // 从消息队列中取出json格式的order，并调用协程实时处理订单。
    conn, err := amqp.Dial("amqp://admin:123123Qq@localhost:5672/")
    failOnError(err, "Failed to connect to RabbitMQ")
    defer conn.Close()
    ch, err := conn.Channel()
    failOnError(err, "Failed to open a channel")
    defer ch.Close()
    // 和send一样也要声明队列， 名字要与send发布的队列一致
    // Note that we declare the queue here, as well. Because we might start the consumer before the publisher, we want to make sure the queue exists before we try to consume messages from it.
    q, err := ch.QueueDeclare(          
      "hello", // name
      false,   // durable
      false,   // delete when unused
      false,   // exclusive
      false,   // no-wait
      nil,     // arguments
    )
    failOnError(err, "Failed to declare a queue")
    // 通过channela通道读消息
    orders, err := ch.Consume(        // returned by amqp::Consume
      q.Name, // queue
      "",     // consumer
      true,   // auto-ack
      false,  // exclusive
      false,  // no-local
      false,  // no-wait
      nil,    // args
    )
    failOnError(err, "Failed to register a consumer")
    // we will read the messages from a channel (returned by amqp::Consume) in a goroutine.
    forever := make(chan bool)
    // 协程，有消息则立即输出
    go func() {                                             
		for d := range msgs {
			HandleOrder(d.Body)
		}
	}()
	// 无消息则阻塞，并打印“Waiting”
    log.Printf(" [*Waiting for Orders. To exit press CTRL+C")
	<-forever        
}

func HandleOrder(OrderJson []byte) {
    username := "root"  //账号
	password := "20210122" //密码
	host := "127.0.0.1" //数据库地址，可以是Ip或者域名
	port := 3306
	Dbname := "CMBS"
    // Json Unmarshal：将json字符串解码到相应的数据结构
    order := &Order{}
    err = json.Unmarshal(OrderJson, order)
    if err != nil {
        return
    }
	StudentID := order.FormValue("StudentID")
	room := order.FormValue("room")
	//连接数据库
	dsn := fmt.Sprintf("%s:%s@tcp(%s:%d)/%s?charset=utf8&parseTime=True&loc=Local", username, password, host, port, Dbname)
	db, err := gorm.Open(mysql.Open(dsn), &gorm.Config{})
	if err != nil {
		fmt.Println(err)
	}
	fmt.Println("------------连接数据库成功-----------")
	isok := 0;

	//查询学生表
	StudentID := StudentID{}
	db.Table("student").Where("StudentID = ?", StudentID).First(&student)
	//查询教室表
	room := room{}
	errClassroom := db.Table("classroom").Where("room = ?", room).First(&classroom)
	if errors.Is(errClassroom, gorm.ErrRecordNotFound){
		isok = 0
	}
	else{
		isok = 1
	}
	if(classroom.seats==classroom.allocated){
		fmt.Println("该教室座位不足！")
	}
	//分配座位
	tx := db.Begin()
	if isok == 1 {
		student.room = classroom.room
		classroom.allocated = classroom.allocated + 1
		tx.Save(&student)
		tx.Save(&classroom)
	} else {
		// 如果未找到，遇到错误时回滚
		tx.ROLLBACK()

	}
	// 否则，提交事务
	tx.Commit()
}

func main() {
	http.HandleFunc("/consumer", ReceiveOrder)    	//设置访问的路径
	err := http.ListenAndServe(":1111", nil)    	//设置监听的端口
	if err != nil {
		log.Fatal("ListenAndServe: ", err)
	}
}