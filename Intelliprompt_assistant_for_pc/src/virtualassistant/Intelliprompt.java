package virtualassistant;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;
import javax.swing.*;
import javax.swing.text.*;
import java.awt.*;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import org.json.JSONArray;
import org.json.JSONObject;

public class Intelliprompt {

    boolean active = true;
    String work = null;
    JFrame frame;
    JTextPane textArea; // Changed to JTextPane for styled text
    JTextField inputField;
    JButton sendButton;
    JButton wakeUpButton;
    JButton sleepButton;

    private static final String API_KEY = System.getenv("GEMINI_API_KEY");
    private static final String API_URL = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" + API_KEY;

    public void initAssistant() {
        createAndShowGUI();
    }

    private void createAndShowGUI() {
        frame = new JFrame("Virtual Assistant");
        frame.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        frame.setSize(400, 300);
        frame.setLayout(new BorderLayout());
        frame.getContentPane().setBackground(Color.LIGHT_GRAY); 

        
        JMenuBar menuBar = new JMenuBar();
        
        JMenu helpMenu = new JMenu("Help");
        JMenuItem helpItem = new JMenuItem("Help");
        helpItem.addActionListener(new ActionListener() {
            @Override
            public void actionPerformed(ActionEvent e) {
                showHelpDialog();
            }
        });
        
        helpMenu.add(helpItem);
        menuBar.add(helpMenu);
        
        JMenuItem clearItem = new JMenuItem("Clear");
        clearItem.addActionListener(new ActionListener() {
            @Override
            public void actionPerformed(ActionEvent e) {
                clearTextArea();
            }
        });
        
        menuBar.add(clearItem); 
        frame.setJMenuBar(menuBar); 
        JLabel titleLabel = new JLabel("Welcome to Your Virtual Assistant", SwingConstants.CENTER);
        titleLabel.setFont(new Font("Arial", Font.BOLD, 16));
        titleLabel.setForeground(Color.BLUE);
        frame.add(titleLabel, BorderLayout.NORTH);

        textArea = new JTextPane(); 
        textArea.setEditable(false);
        textArea.setFont(new Font("Arial", Font.PLAIN, 14)); 
        textArea.setBackground(Color.WHITE); 
        JScrollPane scrollPane = new JScrollPane(textArea);
        frame.add(scrollPane, BorderLayout.CENTER);

        
        JPanel inputPanel = new JPanel();
        inputPanel.setLayout(new BorderLayout());

        inputField = new JTextField();
        inputField.setFont(new Font("Arial", Font.PLAIN, 14)); 

        sendButton = new JButton("Send");
        sendButton.setFont(new Font("Arial", Font.BOLD, 14)); 
        sendButton.addActionListener(new ActionListener() {
            @Override
            public void actionPerformed(ActionEvent e) {
                String userInput = inputField.getText();
                appendText("User    : " + userInput + "\n", Color.red);
                processCommand(userInput);
                inputField.setText("");
            }
        });

        
        wakeUpButton = new JButton("Wake Up");
        wakeUpButton.setFont(new Font("Arial", Font.BOLD, 14));
        wakeUpButton.addActionListener(new ActionListener() {
            @Override
            public void actionPerformed(ActionEvent e) {
                active = true;
                greetings(); 
            }
        });

        sleepButton = new JButton("Sleep");
        sleepButton.setFont(new Font("Arial", Font.BOLD, 14));
        sleepButton.addActionListener(new ActionListener() {
            @Override
            public void actionPerformed(ActionEvent e) {
                active = false;
                appendText("Assistant: I am sleepy! Bye\n", Color.BLACK); 
            }
        });

 
        JPanel buttonPanel = new JPanel();
        buttonPanel.add(wakeUpButton);
        buttonPanel.add(sleepButton);
        
        inputPanel.add(inputField, BorderLayout.CENTER);
        inputPanel.add(sendButton, BorderLayout.EAST);
        frame.add(inputPanel, BorderLayout.SOUTH);
        frame.add(buttonPanel, BorderLayout.NORTH); 

        frame.setVisible(true);
    }

    public void appendText(String text, Color color) {
        StyledDocument doc = textArea.getStyledDocument();
        Style style = textArea.addStyle("Style", null);
        StyleConstants.setForeground(style, color);
        try {
            doc.insertString(doc.getLength(), text, style);
        } catch (BadLocationException e) {
            e.printStackTrace();
        }
    }

    public void clearTextArea() {
        textArea.setText(""); 
        appendText("Assistant: All messages cleared.\n", Color.BLACK); 
    }

    public void processCommand(String command) {
        if (active) {
            if (command.startsWith("good")) {
                greetings();
            } else if (command.equalsIgnoreCase("what is the current time")) {
                getCurrentTime();
            } else if (command.equalsIgnoreCase("open explorer")) {
                openApplication("explorer.exe");
            } else if (command.equalsIgnoreCase("open chrome")) {
                openApplication("chrome.exe");
            } else if (command.equalsIgnoreCase("close chrome")) {
                closeApplication("chrome.exe");
            } else if (command.equalsIgnoreCase("open microsoft edge")) {
                openApplication("microsoft-edge:");
            } else if (command.equalsIgnoreCase("close microsoft edge")) {
                closeApplication("MicrosoftEdge.exe");
            } else if (command.equalsIgnoreCase("open word")) {
                openApplication("winword");
            } else if (command.equalsIgnoreCase("close word")) {
                closeApplication("winword.exe");
            } else if (command.equalsIgnoreCase("open excel")) {
                openApplication("excel");
            } else if (command.equalsIgnoreCase("close excel")) {
                closeApplication("excel.exe");
            } else if (command.equalsIgnoreCase("open powerpoint")) {
                openApplication("powerpnt");
            } else if (command.equalsIgnoreCase("close power point")) {
                closeApplication("powerpnt.exe");
            } else if (command.equalsIgnoreCase("open microsoft store")) {
                openApplication("ms-windows-store:");
            } else if (command.equalsIgnoreCase("close microsoft store")) {
                closeApplication("Microsoft.Store.exe");
            } else if (command.equalsIgnoreCase("open youtube")) {
                openApplication("https://www.youtube.com");
            } else if (command.equalsIgnoreCase("open notepad")) {
                openApplication("notepad");
            } else if (command.equalsIgnoreCase("close notepad")) {
                closeApplication("notepad.exe");
            } else if (command.equalsIgnoreCase("open command prompt")) {
                openApplication("cmd");
            } else if (command.equalsIgnoreCase("close command prompt")) {
                closeApplication("cmd.exe");
            } else if (command.equalsIgnoreCase("open control panel")) {
                openApplication("control");
            } else if (command.equalsIgnoreCase("close control panel")) {
                closeApplication("control.exe");
            } else if (command.equalsIgnoreCase("open task manager")) {
                openApplication("taskmgr");
            } else if (command.equalsIgnoreCase("open calculator")) {
                openApplication("calc");
            } else if (command.equalsIgnoreCase("close calculator")) {
                closeApplication("calculator.exe");
            } else if (command.equalsIgnoreCase("open player")) {
                openApplication("wmplayer");
            } else if (command.equalsIgnoreCase("close player")) {
                closeApplication("wmplayer.exe");
            } else if (command.equalsIgnoreCase("open spotify")) {
                openApplication("spotify");
            } else if (command.equalsIgnoreCase("close spotify")) {
                closeApplication("spotify.exe");
            } else if (command.startsWith("search ")) {
                String query = command.substring(7); 
                searchGoogle(query);
            } else if (command.startsWith("gemini ")) {
                String userInput = command.substring(7); 
                String response = fetchGeminiResponse(userInput);
                appendText("Assistant: Gemini response: " + response + "\n", Color.BLACK); 
            } else {
                appendText("Assistant: Command not recognized.\n", Color.BLACK); 
            }
        }
    }

    public void openApplication(String appName) {
        appendText("Assistant: Opening " + appName + "\n", Color.BLACK);
        work = "cmd /c start " + appName;
        executeCommand();
    }

    public void closeApplication(String appName) {
        appendText("Assistant: Closing " + appName + "\n", Color.BLACK); 
        work = "cmd /c taskkill /im " + appName + " /f";
        executeCommand();
    }

    public void executeCommand() {
        if (work != null) {
            new Thread(() -> {
                try {
                    Process p = Runtime.getRuntime().exec(work);
                    BufferedReader reader = new BufferedReader(new InputStreamReader(p.getInputStream()));
                    String line;
                    while ((line = reader.readLine()) != null) {
                        appendText("Command output: " + line + "\n", Color.GRAY); 
                    }
                    p.waitFor(); // Wait for the process to complete
                } catch (IOException | InterruptedException e) {
                    appendText("Error executing command: " + e.getMessage() + "\n", Color.RED); 
                }
            }).start();
        }
    }

    public void greetings() {
        Calendar calendar = Calendar.getInstance();
        int hour = calendar.get(Calendar.HOUR_OF_DAY);
        String greeting;

        if (hour >= 5 && hour < 12) {
            greeting = "Assistant: Good Morning. How can I help?\n";
        } else if (hour >= 12 && hour < 17) {
            greeting = "Assistant: Good Afternoon. How can I help?\n";
        } else if (hour >= 17 && hour < 21) {
            greeting = "Assistant: Good Evening. How can I help?\n";
        } else {
            greeting = "Assistant: Good Night. How can I help?\n";
        }

        appendText(greeting, Color.BLACK); 
    }

    public void getCurrentTime() {
        DateFormat df = new SimpleDateFormat("hh:mm:ss a"); 
        Date date = new Date();
        appendText("Assistant: The time is " + df.format(date) + "\n", Color.BLACK);
    }

    public void searchGoogle(String query) {
        appendText("Assistant: Searching Google for: " + query + "\n", Color.BLACK); 
        String formattedQuery = query.replace(" ", "+");
        work = "cmd /c start https://www.google.com/search?q=" + formattedQuery;
        executeCommand();
    }

    private String fetchGeminiResponse(String userInput) {
        try {
            String jsonInputString = String.format("{\"contents\":[{\"parts\":[{\"text\":\"%s\"}]}]}", userInput);
            return sendPostRequest(jsonInputString);
        } catch (Exception e) {
            return "Error fetching response: " + e.getMessage();
        }
    }

    private String sendPostRequest(String jsonInputString) throws Exception {
        URL url = new URL(API_URL);
        HttpURLConnection connection = (HttpURLConnection) url.openConnection();
        connection.setRequestMethod("POST");
        connection.setRequestProperty("Content-Type", "application/json");
        connection.setDoOutput(true);
        connection.getOutputStream().write(jsonInputString.getBytes("UTF-8"));

        BufferedReader in = new BufferedReader(new InputStreamReader(connection.getInputStream()));
        StringBuilder response = new StringBuilder();
        String inputLine;

        while ((inputLine = in.readLine()) != null) {
            response.append(inputLine);
        }
        in.close();
        return extractRelevantContent(response.toString());
    }

    private String extractRelevantContent(String jsonResponse) {
        JSONObject jsonObject = new JSONObject(jsonResponse);
        JSONArray candidates = jsonObject.getJSONArray("candidates");
        StringBuilder relevantContent = new StringBuilder();

        if (candidates.length() > 0) {
            JSONObject candidate = candidates.getJSONObject(0);
            JSONObject content = candidate.getJSONObject("content");
            JSONArray parts = content.getJSONArray("parts");

            for (int j = 0; j < parts.length(); j++) {
                JSONObject part = parts.getJSONObject(j);
                String text = part.getString("text").replace("*", "").trim();
                relevantContent.append(text).append("\n");
            }
        }
        return relevantContent.toString().trim();
    }

    private void showHelpDialog() {
        String helpMessage = " Available Commands:\n" +
                "1. Wake Up - Activates the assistant\n" +
                "2. Sleep - Deactivates the assistant\n" +
                "3. good morning / good afternoon / good evening / good night - Greets the user\n" +
                "4. what is the current time - Tells the current time\n" +
                "5. open [application] - Opens specified application (e.g., open chrome)\n" +
                "6. close [application] - Closes specified application (e.g., close chrome)\n" +
                "7. search [query] - Searches Google for the specified query\n" +
                "8. gemini [input] - Interacts with the Gemini API\n" +
                "9. clear - Clears all messages in the chat\n" +
                "10. [other commands as needed]\n";
        
        JOptionPane.showMessageDialog(frame, helpMessage, "Help", JOptionPane.INFORMATION_MESSAGE);
    }

    public static void main(String[] args) {
        Intelliprompt va = new Intelliprompt();
        va.initAssistant();
    }
}