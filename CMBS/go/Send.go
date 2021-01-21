package main

import (
	"log"
	"github.com/streadway/amqp"
)

func failOnError(err error, msg string) {       // helper function to check the return value for each amqp call // 
  // 检查每一步amqp调用的结果
  if err != nil {
    log.Fatalf("%s: %s", msg, err)
  }
}

func main() {
    // 连接Rabbit server
    conn, err := amqp.Dial("amqp://admin:123123Qq@localhost:5672/")             // RabbitMQ 的用户名和密码
    failOnError(err, "Failed to connect to RabbitMQ")                           // 检查调用的结果
    defer conn.Close()
    // 创建channel（which is where most of the API for getting things done resides）
    ch, err := conn.Channel()
    failOnError(err, "Failed to open a channel")
    defer ch.Close()
    // 声明一个队列
    q, err := ch.QueueDeclare(
      "hello", // name
      false,   // durable
      false,   // delete when unused
      false,   // exclusive
      false,   // no-wait
      nil,     // arguments
    )
    failOnError(err, "Failed to declare a queue")
    // 发送消息到队列中
    body := "Hello World!"
    err = ch.Publish(
      "",     // exchange
      q.Name, // routing key
      false,  // mandatory
      false,  // immediate
      amqp.Publishing {
        ContentType: "text/plain",
        Body:        []byte(body),       // The message content is a byte array, so you can encode whatever you like there
      })
    failOnError(err, "Failed to publish a message")
    
}
