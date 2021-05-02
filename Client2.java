package com.company;

import java.io.DataInputStream;
import java.io.DataOutputStream;
import java.io.IOException;
import java.net.Socket;
import java.util.Scanner;

public class Client2 {


    public static void main(String args[]) throws IOException, InterruptedException {

        Socket chrome = new Socket("127.0.0.1", 2020);

        DataOutputStream writer = new DataOutputStream(chrome.getOutputStream());

        DataInputStream reader = new DataInputStream(chrome.getInputStream());

        boolean close = false;

        String id = new String(reader.readUTF());


        Scanner readOperation = new Scanner(System.in);

        while (!close){
            System.out.println("1) For Send Data To Server");
            System.out.println("2) For Broadcasting");
            System.out.println("3) EXIT");

            int operation = Integer.parseInt(readOperation.nextLine());

            switch (operation){
                case 1:
                    System.out.println("Enter Your Message : ");
                    String serverMessage = readOperation.nextLine();
                    writer.writeUTF("to:server" + serverMessage);
                    System.out.println(new String(reader.readUTF()));
                    break;
                case 2:
                    System.out.println("Enter Broadcast Message : ");
                    String broadcastMessage = readOperation.nextLine();
                    writer.writeUTF("to:online" + broadcastMessage);
                    System.out.println(new String(reader.readUTF()));
                    break;
                case 3:
                    writer.writeUTF("exit:" + id);
                    System.out.println(new String(reader.readUTF()));
                    reader.close();
                    writer.close();
                    chrome.close();
                    break;

            }

            if (operation == 3)
                close = true;
        }

    }
}
