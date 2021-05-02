package com.company;

import java.net.*;
import java.io.*;

public class Client1 {


    public static void main(String args[]) throws IOException, InterruptedException {


        for (int i = 1; i < 5; i++){
            Socket chrome = new Socket("127.0.0.1", 2020);

            DataOutputStream writer = new DataOutputStream(chrome.getOutputStream());
            DataInputStream reader = new DataInputStream(chrome.getInputStream());

            String body = "ping";
            writer.writeUTF(body);
            System.out.println(body);
            Thread.sleep(1200);
            String message = new String(reader.readUTF());
            System.out.println(message);

            reader.close();
            writer.close();
            chrome.close();

            Thread.sleep(1000);


        }



    }
}
