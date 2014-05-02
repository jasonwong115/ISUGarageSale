package home.fragments;

import android.os.AsyncTask;
import info.androidhive.slidingmenu.model.UserLoginInfo;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.net.URI;
import java.net.URL;
import java.util.ArrayList;
import java.util.List;
import java.util.Scanner;

import main.development.JSONParser;

import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import android.util.Log;

public class LoginTask extends AsyncTask<String, Integer, UserLoginInfo> {
	private static String url = "webservice/login.php";
	private static final String TAG_SUCCESS = "success";
	private UserLoginInfo user;

	protected UserLoginInfo doInBackground(String... input) {
		String handle = input[0];
		String password = input[1];
		user = null;
		String uid = "";
		int id = 0;
		
		List<NameValuePair> params = new ArrayList<NameValuePair>();
		params.add(new BasicNameValuePair("handle", handle));
		params.add(new BasicNameValuePair("password", password));

		// getting JSON Object
		// Note that create product url accepts POST method
		JSONParser jsonParser = new JSONParser();
		JSONObject json = jsonParser.makeHttpRequest(url,
				"POST", params);
		
		// check for success tag
		try {
			int success = json.getInt(TAG_SUCCESS);
			Log.d("Create Response", json.toString());
			if (success == 1) {
				id = json.getInt("id");
				uid = json.getString("uid");
				user = new UserLoginInfo(id, uid);
			} else {
				return null;
			}
		} catch (Exception e) {
			user = null;
		}
		return user;
	}

}
