const express = require('express');
const bodyParser = require('body-parser');

const app = express();
app.use(bodyParser.urlencoded({ extended: false }));

const languages = {
  "1": "English",
  "2": "French",
  "3": "Kinyarwanda",
  "4": "Swahili"
};

const phrases = {
  "1": {
    "1": ["Hello", "Həˈloʊ"],
    "2": ["Good Morning", "ˌɡʊd ˈmɔːrnɪŋ"],
    "3": ["How are you?", "haʊ ɑːr juː"]
  },
  "2": {
    "1": ["Bonjour", "bɔ̃ʒuʁ"],
    "2": ["Bonsoir", "bɔ̃swaʁ"],
    "3": ["Comment ça va?", "kɔmɑ̃ sa va"]
  }
  // Add more phrases for Kinyarwanda and Swahili
};

app.post('/', (req, res) => {
  const { sessionId, serviceCode, phoneNumber, text } = req.body;

  let response = '';
  const textParts = text.split('*');

  if (text === '') {
    response = 'CON Welcome to the Language Assistance Tool. Select a language:\n';
    for (const [key, value] of Object.entries(languages)) {
      response += `${key}. ${value}\n`;
    }
  } else {
    if (textParts.length === 1) {
      const languageId = textParts[0];
      response = 'CON Select a category:\n1. Greetings\n2. Directions\n3. Dining\n4. Emergencies';
    } else if (textParts.length === 2) {
      const languageId = textParts[0];
      const categoryId = textParts[1];
      response = 'CON Select a phrase:\n';
      for (const [key, value] of Object.entries(phrases[categoryId])) {
        response += `${key}. ${value[0]}\n`;
      }
    } else if (textParts.length === 3) {
      const languageId = textParts[0];
      const categoryId = textParts[1];
      const phraseId = textParts[2];
      const phrase = phrases[categoryId][phraseId][0];
      const pronunciation = phrases[categoryId][phraseId][1];
      response = `CON ${phrase} (${pronunciation})\n1. Send via SMS\n2. Back to main menu`;
    } else if (textParts.length === 4) {
      const option = textParts[3];
      if (option === '1') {
        response = 'END The phrase has been sent to your phone.';
      } else {
        response = 'CON Welcome to the Language Assistance Tool. Select a language:\n';
        for (const [key, value] of Object.entries(languages)) {
          response += `${key}. ${value}\n`;
        }
      }
    }
  }

  res.set('Content-Type', 'text/plain');
  res.send(response);
});

const port = process.env.PORT || 3000;
app.listen(port, () => {
  console.log(`Server running on port ${port}`);
});
