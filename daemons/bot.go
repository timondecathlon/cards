package main

import (
    "fmt"
	"net/http"
	//"bytes"
	"io"
	"io/ioutil"
	"encoding/json"
	"strconv"
	"time"
	"os/exec"
	"os"
	"strings"
)


type GetMeT struct {
	Ok     bool `json:"ok"`
	Result GetMeResultT `json:"result"`
}

type GetMeResultT struct {
	Id        int `json:"id"`
	IsBot     bool `json:"is_bot"`
	FirstName string `json:"first_name"`
	Username  string `json:"username"`
}

type GetUpdatesT struct {
	Ok     bool `json:"ok"`
	Result []GetUpdatesResultT `json:"result"`
}

type GetUpdatesResultT struct {
	UpdateID int `json:"update_id"`
	Message  MessageT `json:"message,omitempty"`
}

type MessageT struct {
	MessageID int `json:"message_id"`
	From      GetUpdatesResultMessageFromT `json:"from"`
	Chat      GetUpdatesResultMessageChatT `json:"chat"`
	Date     int `json:"date"`
	Text      string `json:"text"`
}

type GetUpdatesResultMessageFromT struct {
	ID				int     `json:"id"`
	IsBotFirstName  bool    `json:"is_bot"`
	FirstName       string  `json:"first_name"`
	Username        string  `json:"username"`
	LanguageCode    string  `json:"language_code"`
}

type GetUpdatesResultMessageChatT struct {
	Id	       int     `json:"id"`
	FirstName  string  `json:"ifirst_named"`
	Username   string  `json:"username"`
	Type       string  `json:"type"`
}

const telegramBaseUrl = "https://api.telegram.org/bot"
const telegramToken = "542474368:AAEm9QSHOQpOgvoKgvsrsFPOnmYhXHwmwjU";

const methodGetMe = "getMe"
const methodGetUpdates = "getUpdates"

func main() {

	last_update:= 0
	//last_update, _ := strconv.Atoi(string(fileGetContents("last_update.txt")))


	ticker := time.NewTicker(time.Second * 3)
    //go func() {
        for t := range ticker.C {
			body := getUpdates(last_update)

			getUpdates := GetUpdatesT{}

			json.Unmarshal(body, &getUpdates)

			for _, item := range(getUpdates.Result) {
				sendMessage(item.Message.Chat.Id,item.Message.Text)

				//нужно научиться отрывать элементы от массива
				words := strings.Split(item.Message.Text," ")

				if (words[0] == "bash") {
					go func () {

						arrayShiftString(words)

						service := words[0]

						//arrayShiftString(words)

						str := strings.Join(words," ")

						//exec.Command("/bin/sh", "-c", "sudo "+ str)

						exec.Command("bash", "-c", "sudo "+ str).Output()
						//автоматически ввожу пароль для суперюзера
						exec.Command("bash", "-c", "root $ echo 20091993dec | sudo program").Output()

						// на запуске прилог тут висит
						//exec.Command(service ,str).Run()

						sendMessage(item.Message.Chat.Id,service)

						sendMessage(item.Message.Chat.Id,str)

						//напрямую работает через подстановки почему то нет
						//exec.Command("kill" ,"71849").Run()

					}()

				}






				//exec.Command("kill" ,item.Message.Text).Run()

			    if (item.Message.Text == "создай файл") {
					exec.Command("touch" ,"anna.txt").Run()
				}

				last_update = item.UpdateID
			}

			fmt.Println("Tick at", t)
        }
	//}()

	//без этого не работает
	//time.Sleep(time.Second * 20)

	//text := strconv.Itoa(last_update)
	//filePutConterntsString("last_update.txt", text)


	//exec.Command("go" ,"run","bot.go").Run()

	/*
	//получаем данные по url
	body := getUpdates(last_update)

	//создаем новый экземпляр структуры в которую будет закидывать данные ил json
	getUpdates := GetUpdatesT{}

	//выгружаю данные из JSON в экземпляр структуры по указателю
	json.Unmarshal(body, &getUpdates)

	//для каждого элемента в рехультате

	for _, item := range(getUpdates.Result) {
		fmt.Println(item.Message.Text)
		sendMessage(item.Message.Chat.Id,item.Message.Text)
	}
	*/


}

func getUrlByMethod(methodName string) string {
	return telegramBaseUrl + telegramToken + "/" + methodName
}

//функция для отправки сообщения
func getUpdates(update_id int) []byte {
	update_id = update_id + 1
	url := getUrlByMethod("getUpdates") + "?offset="+strconv.Itoa(update_id)
	return getBodyByUrl(url)
}

//функция для отправки сообщения
func sendMessage(chat_id int, text string) {
	url := getUrlByMethod("sendMessage") + "?chat_id="+strconv.Itoa(chat_id)+"&text=" + text
	http.Get(url)
}


//функция сходить по url и получить ответ в виде среза байт - только так для джейсона
func getBodyByUrl( url string) []byte {

	//отправляем запрос на адрес без параметров (ошибку кидаем в пустой буфер)
	response, _ := http.Get(url)

	//отсрочено закрываем ответ
	defer response.Body.Close()

	//считываем все в переменную
	body, _ := ioutil.ReadAll(response.Body)

	return body
}

func filePutConterntsString(filename string, data string) bool {

	fmt.Println("Новое значение", data)
    file, err := os.Create("last_update.txt")

    if err != nil{
        fmt.Println("Unable to create file:", err)
        return false
    }
	defer file.Close()

	file.WriteString(data)

	return true
}

//функция чтобы просто получить текстовый ответ от адреса
func fileGetContents(filename string) []byte {  
	//считываю id последнего обновления из файла
	file, _ := os.Open(filename)
	defer file.Close()
	data := make([]byte, 64)
    for{
        _, err := file.Read(data)
        if err == io.EOF{   // если конец файла
            break           // выходим из цикла
		}
	}
	return data
}

//функция чтобы просто получить текстовый ответ от адреса
func urlGetContents(url string) string {
	response, _ := http.Get(url)
	defer response.Body.Close()
	body, _ := ioutil.ReadAll(response.Body)
	return string(body)
}




func setInterval(f func() , interval int)  {
	ticker := time.NewTicker(time.Second * time.Duration(interval))
    for t := range ticker.C {



		fmt.Println("Tick at", t)
	}
}

func arrayShiftString(arr []string) []string {
	i := 0

	// 1. Выполнить сдвиг a[i+1:] влево на один индекс.
	copy(arr[i:], arr[i+1:])

	// 2. Удалить последний элемент (записать нулевое значение).
	arr[len(arr)-1] = ""

	// 3. Усечь срез.
	arr = arr[:len(arr)-1]

	return arr
}
