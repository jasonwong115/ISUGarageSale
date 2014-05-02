package home.fragments;

import info.androidhive.slidingmenu.R;
import info.androidhive.slidingmenu.model.LoginDatabaseHandler;
import info.androidhive.slidingmenu.model.UserLoginInfo;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.net.URI;
import java.util.ArrayList;
import java.util.List;
import java.util.Scanner;
import java.util.concurrent.ExecutionException;

import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import main.development.JSONParser;
import main.development.MainActivity;
import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ListView;
import android.widget.TextView;

public class LogoutFragment extends Fragment implements OnClickListener {
	View rootView;
	LoginDatabaseHandler db;
	MainActivity main;
	JSONParser jsonParser = new JSONParser();
	private static final String url = "webservice/logout.php";
	private static final String TAG_SUCCESS = "success";

	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstanceState) {
		rootView = inflater.inflate(R.layout.fragment_logout, container, false);
		main = (MainActivity) getActivity();
		db = main.getDbHandler();

		TextView output = (TextView) rootView.findViewById(R.id.logout_output);
		Button b = (Button) rootView.findViewById(R.id.logout_button);
		b.setOnClickListener(this);

		if (db.isLoggedIn()) {
			output.setText("Logged In");
		} else {
			output.setText("Logged Out");
		}
		return rootView;
	}

	@Override
	public void onClick(View v) {
		EditText output = (EditText) rootView.findViewById(R.id.output);
		LogoutTask task = (LogoutTask) new LogoutTask().execute(db.getUID());
		int response = 0;
		try {
			response = task.get();
		} catch (InterruptedException e) {
			e.printStackTrace();
		} catch (ExecutionException e) {
			e.printStackTrace();
		}
		if(response == 1){
			db.removeLogin();
			Intent i = new Intent(getActivity(), MainActivity.class);
			startActivity(i);
		}else{
			output.setText("Logout failed");
		}
	}

	/**
	 * Background Async Task to Create new product
	 * */
	class LogoutTask extends AsyncTask<String, String, Integer> {

		/**
		 * Before starting background thread Show Progress Dialog
		 * */
		@Override
		protected void onPreExecute() {

		}

		/**
		 * Creating product
		 * */
		protected Integer doInBackground(String... input) {

			String uid = input[0];

			// Building Parameters
			List<NameValuePair> params = new ArrayList<NameValuePair>();
			 params.add(new BasicNameValuePair("uid",uid));
			 
			JSONObject json = jsonParser.makeHttpRequest(url,
					"POST", params);
			
			// check for success tag
			try {
				Log.d("Create Response", json.toString());
				int success = json.getInt(TAG_SUCCESS);
				return success;
				
			} catch (Exception e) {
				e.printStackTrace();
			}

			return 0;
		}

		/**
		 * After completing background task Dismiss the progress dialog
		 * **/
		protected void onPostExecute(String file_url) {

		}

	}

}
