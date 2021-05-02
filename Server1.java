package com.company;

import java.net.*;
import java.io.*;

public class Server1 {


    public static void main(String args[]) throws IOException {


        ServerSocket africaServer = new ServerSocket(2020);

        System.out.println("server listening for port localhost:2020");

        while (true){

            Socket client = africaServer.accept();

            DataInputStream reader = new DataInputStream(client.getInputStream());
            DataOutputStream writer = new DataOutputStream(client.getOutputStream());

            String message = new String(reader.readUTF());

            if(message.toLowerCase().equals("ping"))
                writer.writeUTF("pong");
            else
                writer.writeUTF("Not the right word");

            reader.close();
            writer.close();
            client.close();
        }

    }

}
