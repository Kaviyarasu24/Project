import pyttsx3 #text conversion library into speech
import speech_recognition as sr
import keyboard
import os
import subprocess as sp
os.environ["API_KEY"] = "AIzaSyBR242uQhGCgHK_59uWX5-oYcnkQ8EJ-r0"
import google.generativeai as genai
from decouple import config
from datetime import datetime
from random import choice
from conv import random_text
from online import find_my_ip, search_on_google, search_on_wikipedia, youtube




engine=pyttsx3.init('sapi5')
engine.setProperty('volume',1.0)
engine.setProperty('rate',200)
voices=engine.getProperty('voices')
engine.setProperty('voice',voices[0].id) # 1 female,0 male



USER=config('CAPTAIN',default='Captain')
HOSTNAME=config('EDITH',default='Edith')
genai.configure(api_key=os.environ["API_KEY"])






def speak(text):
    engine.say(text)
    engine.runAndWait()



def greet_me():

    hour = datetime.now().hour
    if(hour >=6) and (hour <12):
        speak(f"Good morning{USER}")
    elif(hour>=12)and (hour<=16):
        speak(f"Good Afternoon{USER}")
    elif(hour>=16)and (hour<19):
        speak(f"Good evening{USER}")
    else:
        speak("Good night")
    speak(f"I am {HOSTNAME}. How may i assist you? {USER}")

listening=False
def start_listening():
    global listening
    listening=True
    print("started listening.")


def pause_listening():
    global listening
    listening=False
    print("Stopped listening.")

keyboard.add_hotkey('ctrl+b',start_listening)
keyboard.add_hotkey('ctrl+t',pause_listening)


def take_command():
    r=sr.Recognizer()
    with sr.Microphone() as source:
        print("Listening...")
        r.pause_threshold=1
        audio=r.listen(source)

    try:
        print("Recoginizing....")
        queri=r.recognize_google(audio,language='en-in')
        print(queri)
        if 'stop' in queri or 'close' in queri:
             hour=datetime.now().hour
             if hour>=21 and hour<6:
                speak("Good night sir,take care!")
             else:
                speak("Have a good day sir!")
             exit()
            
        else:
           speak(choice(random_text))


    except Exception:
        speak("Sorry I couldm't understand.Can you please repeat that?")
        queri='None'
    return queri
    

if __name__=='__main__':
    greet_me()

    while True:
        if listening:
            query=take_command().lower()
            if "how are you" in query:
                speak("I am absolutely fine sir.What about you?")
            elif"open command prompt" in query:
                speak("opening command prompt sir")
                os.system("start cmd")
            elif"open camera"in query:
                speak("opening camera sir ")
                sp.run("start microsoft.windows.camera:",shell=True)
            elif"open calculator"in query:
                speak("Opening calculator sir.")
                os.system("calc.exe")
            elif("open browser")in query:
                speak("opening browser sir.")
                edge_path = r"C:\Users\kaviyarasu p\Desktop\Microsoft Edge.lnk"
                url = "https://www.google.com"
                os.system(f'"{edge_path}" {url}')
            elif("ip address")in query:
                ip_address=find_my_ip()
                speak(f"your ip address is {ip_address}")
                print(f"Your id address is {ip_address}")
            elif("open youtube"in query):
                speak("What's your topic to be search ,sir")
                video=take_command().lower()
                youtube(video)
            elif("open google")in query:
                speak(f"What to be search?,{USER}")
                query=take_command().lower()
                search_on_google(query)
            elif("wikipedia")in query:
                speak(f"What do you want to search on wikipedia ,{USER}")
                question=take_command().lower()
                result=search_on_wikipedia(question)
                print(result)
                speak(f"According to wikipedia,{result}")

            


            """else:
                user_input = query
                model = genai.GenerativeModel("gemini-1.5-flash")
                response = model.generate_content(user_input)
                print(response.text)
                speak(response.text)"""
                
            


