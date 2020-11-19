package main

import (
    "fmt"
	"time"
	"os/exec"


)

func main() {

	ticker := time.NewTicker(time.Second * 30)
        for t := range ticker.C {

			go func () {

				//exec.Command("bash", "-c", "sudo  php  /var/www/www-root/data/www/cards.bitkit.pro/workers/notificator.php").Output()
                //exec.Command("php", "notificator.php").Output()
                //автоматически ввожу пароль для суперюзера
                //exec.Command("bash", "-c", "root $ echo MvLD6d6NeaSqqc  | sudo program").Output()

                exec.Command("php",  "/var/www/www-root/data/www/cards.bitkit.pro/workers/notificator.php").Run()
			}()

			fmt.Println("Tick at", t)
        }

	//без этого не работает
	//time.Sleep(time.Second * 20)

}

