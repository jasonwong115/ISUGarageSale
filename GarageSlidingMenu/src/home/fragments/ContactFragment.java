package home.fragments;

import info.androidhive.slidingmenu.R;

import java.util.ArrayList;
import java.util.List;

import main.development.FailureDialogFragment;
import main.development.JSONParser;
import main.development.SubmittedDialogFragment;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.ProgressDialog;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v4.app.DialogFragment;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentTransaction;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.EditText;
import android.widget.RadioButton;
import android.widget.RadioGroup;

/**
 * Fragment used if user wants to submit a contact form The submission creates a
 * new row in the MySQL database The table used is "gs_contact"
 * 
 * @author jasonwong
 * 
 */
public class ContactFragment extends Fragment {
	
	//Global variables needed so async task is aware of all elements in the view
	EditText inputName;
	EditText inputEmail;
	EditText inputSubject;
	EditText inputMessage;
	RadioGroup inputReason;
	RadioButton selectedReason;
	ProgressDialog pDialog;
	View rootView;

	// URL for the webservice
	private static String url_create_product = "webservice/insert_contact.php";
	private static final String TAG_SUCCESS = "success";

	// Constructor does nothing
	public ContactFragment() {
	}

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstanceState) {

		// Assign variables to right elements found in the view
		rootView = inflater
				.inflate(R.layout.fragment_contact, container, false);
		Button btnSubmitContact = (Button) rootView
				.findViewById(R.id.submitContact);
		inputName = (EditText) rootView.findViewById(R.id.inputName);
		inputEmail = (EditText) rootView.findViewById(R.id.inputEmail);
		inputSubject = (EditText) rootView.findViewById(R.id.inputSubject);
		inputMessage = (EditText) rootView.findViewById(R.id.inputMessage);
		inputReason = (RadioGroup) rootView.findViewById(R.id.inputReason);

		// On the click of the button check for valid input
		btnSubmitContact.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View view) {
				// creating new product in background thread
				String name = inputName.getText().toString();
				String email = inputEmail.getText().toString();
				String message = inputMessage.getText().toString();
				String subject = inputSubject.getText().toString();

				if (name.trim().equalsIgnoreCase("")
						|| email.trim().equalsIgnoreCase("")
						|| message.trim().equalsIgnoreCase("")
						|| subject.trim().equalsIgnoreCase("")) {
					// Unset all errors first
					inputName.setError("null");
					inputEmail.setError("null");
					inputMessage.setError("null");
					inputSubject.setError("null");

					// Display error to user for any invalid input
					if (name.trim().equalsIgnoreCase("")) {
						inputName.setError("Enter your name!");
					}
					if (email.trim().equalsIgnoreCase("")) {
						// Overwrites error from LDAP if email never given in
						// first place
						inputEmail.setError("Enter your email!");
					}
					if (message.trim().equalsIgnoreCase("")) {
						inputMessage.setError("Enter a message!");
					}
					if (subject.trim().equalsIgnoreCase("")) {
						inputSubject.setError("Enter a subject!");
					} // End of if

					// If the input is correct, insert the contact into the
					// database
				} else {
					new InsertContact().execute();
				} // End of else

			}// End of onClick
		}); // End of OnClickListener
		return rootView;
	}

	/*
	 * Dialog to be shown on successful run of the web service
	 */
	public void showSuccessDialog() {
		FragmentTransaction fm = getActivity().getSupportFragmentManager()
				.beginTransaction();
		DialogFragment dialog = SubmittedDialogFragment
				.newInstance("Thanks for contacting us!");
		// Create an instance of the dialog fragment and show it
		dialog.show(fm, "SubmittedDialogFragment");
	}

	/*
	 * Dialog to be shown on failure run of the web service
	 */
	public void showFailureDialog() {
		FragmentTransaction fm = getActivity().getSupportFragmentManager()
				.beginTransaction();
		DialogFragment dialog = FailureDialogFragment
				.newInstance("Error occurred trying to submit contact form!");
		// Create an instance of the dialog fragment and show it
		dialog.show(fm, "FailureDialogFragment");
	}

	/**
	 * Background Async Task to insert the contact into the database
	 * */
	class InsertContact extends AsyncTask<String, String, String> {

		/*
		 * Before starting background thread Show Progress Dialog
		 */
		@Override
		protected void onPreExecute() {
			super.onPreExecute();
			pDialog = new ProgressDialog(getActivity());
			pDialog.setMessage("Submitting info...");
			pDialog.setIndeterminate(false);
			pDialog.setCancelable(true);
			pDialog.show();
		}

		/**
		 * Insert contact using web service
		 * */
		protected String doInBackground(String... args) {

			// Gather the information the user input into the form
			String name = inputName.getText().toString();
			String email = inputEmail.getText().toString();
			String subject = inputSubject.getText().toString();
			String message = inputMessage.getText().toString();
			// Need to do special actions to retrieve selected radio button from
			// radio group
			int reasonID = inputReason.getCheckedRadioButtonId();
			selectedReason = (RadioButton) rootView.findViewById(reasonID);
			String reason = selectedReason.getText().toString();

			// Building Parameters to send to the webservice
			List<NameValuePair> params = new ArrayList<NameValuePair>();
			params.add(new BasicNameValuePair("name", name));
			params.add(new BasicNameValuePair("email", email));
			params.add(new BasicNameValuePair("subject", subject));
			params.add(new BasicNameValuePair("message", message));
			params.add(new BasicNameValuePair("reason", reason));

			// getting JSON Object
			JSONParser jsonParser = new JSONParser();
			JSONObject json = jsonParser.makeHttpRequest(url_create_product,
					"POST", params);

			// check log cat for response
			Log.d("Create Response", json.toString());

			// check for success tag from web service
			try {
				int success = json.getInt(TAG_SUCCESS);
				if (success == 1) {
					// Show Success Dialog
					showSuccessDialog();
				} else {
					// Show Failure Dialog
					showFailureDialog();
				}
			} catch (JSONException e) {
				showFailureDialog();
			}
			return null;
		}

		/*
		 * After completing background task Dismiss the progress dialog
		 */
		protected void onPostExecute(String file_url) {
			// dismiss the dialog once done
			pDialog.dismiss();

		}

	}
}
