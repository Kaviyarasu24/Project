import telebot
import google.generativeai as genai

# Replace 'your-telegram-bot-token' and 'your-gemini-api-key' with your actual tokens
TELEGRAM_BOT_TOKEN = 'telegram_bot_token'
GEMINI_API_KEY = 'gemini_api_key'
GEMINI_API_URL = 'https://api.gemini.com/v1/chatbot-response'  # Replace with the correct Gemini API URL if different

# Initialize the bot
bot = telebot.TeleBot(TELEGRAM_BOT_TOKEN)

@bot.message_handler(commands=['start', 'help'])
def send_welcome(message):
    bot.reply_to(message, "Welcome! I'm your chatbot powered by Gemini. How can I assist you today?")

@bot.message_handler(func=lambda message: True)
def handle_message(message):
    def clean_text(input_text):
        # Split the text into lines
        lines = input_text.split("\n")

        # Create a list to store cleaned lines
        cleaned_lines = []

        for line in lines:
            # Remove leading asterisks (*) and excess spaces
            cleaned_line = line.strip("** ").strip()
            if cleaned_line:  # Skip empty lines
                # Append a new line (\n) before adding new topics/subtopics
                cleaned_lines.append(f"{cleaned_line}\n")
        

        
        cleaned_text = "".join(cleaned_lines)
        return cleaned_text

    user_input = message.text

    try:
        model = genai.GenerativeModel("gemini-1.5-flash")
        response = model.generate_content(user_input)

        # Extract and clean the text content from the response
        raw_content = response.candidates[0].content.parts[0].text
        cleaned_content = clean_text(raw_content)

        # Reply with the cleaned content
        bot.reply_to(message, cleaned_content)

    except Exception as e:
        bot.reply_to(message, f"An error occurred: {e}")

# Polling the bot
try:
    print("Bot is running...")
    bot.polling()
except KeyboardInterrupt:
    print("Bot stopped.")
