// Our consumer listens for messages from RabbitMQ.
// so unlike the publisher which publishes a single message, we'll keep the consumer running to listen for messages and print them out.

package main

import (
  "log"
  "github.com/streadway/amqp"
)

func failOnError(err error, msg string) {
  if err != nil {
    log.Fatalf("%s: %s", msg, err)
  }
}

func main() {
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
    msgs, err := ch.Consume(        // returned by amqp::Consume
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
			log.Printf("Received a message: %s", d.Body)
		}
	}()
	// 无消息则阻塞，并打印“Waiting”
    log.Printf(" [*Waiting for messages. To exit press CTRL+C")
	<-forever                                               
    
}