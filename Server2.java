package com.company;


import java.io.DataInputStream;
import java.io.DataOutputStream;
import java.io.IOException;
import java.net.ServerSocket;
import java.net.Socket;
import java.util.HashMap;

public class Server2 {

    static HashMap<Integer, Socket> onlineUsers = new HashMap<Integer, Socket>();
    static int count = 0;

    public static void main(String args[]) throws IOException {

        ServerSocket africaServer = new ServerSocket(2020);
        System.out.println("server listening for port localhost:2020..");

        while (true){

            Socket client = africaServer.accept();
            Server2.count++;
            int socketId = Server2.count;
            Server2.onlineUsers.put(socketId, client);

            DataInputStream reader = new DataInputStream(client.getInputStream());
            DataOutputStream writer = new DataOutputStream(client.getOutputStream());

            writer.writeUTF(socketId+"");

            String message = new String(reader.readUTF());

            String to = "";
            try {
                to = message.substring(0,9);
            }catch (Exception exception){
                to = message.substring(0);
            }

            switch (to){
                case "to:server":
                    String body = message.substring(9);
                    System.out.println(body);
                    writer.writeUTF("your message delivered success " + body);
                    break;
                case "to:online":
                    for (int i = 1; i < Server2.onlineUsers.size() ; i++){
                       try {
                           if (Server2.onlineUsers.containsKey(i)){
                               String tmp = message.substring(9);
                               DataOutputStream bcm = new DataOutputStream(Server2.onlineUsers.get(i).getOutputStream());
                               bcm.writeUTF("Broadcast Message :  " + tmp);
                               bcm.close();
                           }
                       }catch (Exception exception){}
                    }
                    break;
                default:
                    Server2.onlineUsers.remove(to.replaceFirst("exit:",""));
                    writer.writeUTF("please come back again !");
                    reader.close();
                    writer.close();
                    client.close();
                    break;
            }

        }

    }


}
