package home.fragments;

import java.io.IOException;
import java.io.PrintWriter;
import java.net.Socket;
import java.net.URL;
import java.net.UnknownHostException;
import java.util.Scanner;

import android.os.AsyncTask;

public class NetworkTask extends AsyncTask<Void, Integer, char[]> {
	// Do the long-running work in here
	protected char[] doInBackground(Void... params) {
		// Socket socket = new Socket(host,80);
		String host = "proj-309-07.cs.iastate.edu";
		String root = "/07/trunk/app/query.php?id=2";

		// InetAddress dstAddress = InetAddress.getByName(host);
		Socket s = null;
		try {
			s = new Socket(host, 80);
		} catch (UnknownHostException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		PrintWriter out = null;
		try {
			out = new PrintWriter(s.getOutputStream());
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		Scanner in = null;
		try {
			in = new Scanner(s.getInputStream());
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

		String query = "";
		query += "GET " + root + " HTTP/1.1\r\n";
		query += "Host: " + host + "\r\n";
		query += "\r\n";

		out.print(query);
		out.flush();
		String x = "";
		while (in.hasNextLine()) {
			x += in.nextLine();
			// System.out.println(x);

		}
		try {
			s.close();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		return x.toCharArray();
	}



}
