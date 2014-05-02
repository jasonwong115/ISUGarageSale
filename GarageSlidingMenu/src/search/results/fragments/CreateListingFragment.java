package search.results.fragments;


import info.androidhive.slidingmenu.R;
import info.androidhive.slidingmenu.model.LoginDatabaseHandler;

import java.util.ArrayList;
import java.util.List;

import main.development.FailureDialogFragment;
import main.development.JSONParser;
import main.development.MainActivity;
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
/**
 * 
 * @author jasonwong
 * Fragment allows user to create a listing to sell their item
 */
public class CreateListingFragment extends Fragment{
	
	//Text inputted by user
	EditText inputDescription;
	EditText inputAskingPrice;
	EditText inputOtherOffer;
	EditText inputTitle;
	
	//Extra useful variables
	ProgressDialog pDialog;
	View rootView;
	
	private static String url_create_product = "webservice/insert_listing.php";
	private static final String TAG_SUCCESS = "success";
	
	//Constructor, does nothing
	public CreateListingFragment(){}
	

	@Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
            Bundle savedInstanceState) {
		//Load proper inputs
        rootView = inflater.inflate(R.layout.fragment_create_listing, container, false);
        Button btnSubmitContact = (Button) rootView.findViewById(R.id.submitCreateListing);
		inputDescription = (EditText) rootView.findViewById(R.id.inputDescription);
		inputAskingPrice = (EditText) rootView.findViewById(R.id.inputAskingPrice);
		inputOtherOffer = (EditText) rootView.findViewById(R.id.inputOtherOffer);
		inputTitle = (EditText) rootView.findViewById(R.id.inputTitle);
		
		//Wait for user to submit form
        btnSubmitContact.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View view) {
				String description = inputDescription.getText().toString();
				String title = inputTitle.getText().toString();
				String asking_price = inputAskingPrice.getText().toString();
				String other_offer = inputOtherOffer.getText().toString();
				
				//Check if description and title are included (required)
				if(description.trim().equalsIgnoreCase("") || title.trim().equalsIgnoreCase("")
						|| (asking_price.trim().equalsIgnoreCase("") && other_offer.trim().equalsIgnoreCase("")))
		        {
					if(description.trim().equalsIgnoreCase("")){
						inputDescription.setError("Enter a description!");
					} if(title.trim().equalsIgnoreCase("")){
						inputTitle.setError("Enter a title!");
					} if(asking_price.trim().equalsIgnoreCase("")){
						inputAskingPrice.setError("Enter an asking price or another trade offer!");
					} if(other_offer.trim().equalsIgnoreCase("")){
						inputOtherOffer.setError("Enter trade offer or an asking price!");
					} // End of if
		        }else
		        {
		        	new InsertListing().execute();
		        } // End of else
				
				
			} // End of onClick
		}); // End of OnClickListener
        return rootView;
    }
	
	/*
	 * Dialog to show if listing has been successfully submitted to the database
	 */
	public void showSuccessDialog() {
		FragmentTransaction fm = getActivity().getSupportFragmentManager().beginTransaction();
		DialogFragment dialog = SubmittedDialogFragment.newInstance("Your listing has been created!");
        // Create an instance of the dialog fragment and show it
        dialog.show(fm, "SubmittedDialogFragment");
    }
	
	/*
	 * Dialog to show if listing has not been successfully submitted to the database
	 */
	public void showFailureDialog() {
		FragmentTransaction fm = getActivity().getSupportFragmentManager().beginTransaction();
		DialogFragment dialog = FailureDialogFragment.newInstance("Error occurred trying to retrieve listings!");
        // Create an instance of the dialog fragment and show it
        dialog.show(fm, "FailureDialogFragment");
    }
	
	/**
	 * Background Async Task to Create new product
	 * */
	class InsertListing extends AsyncTask<String, String, String> {

		/**
		 * Before starting background thread Show Progress Dialog
		 * */
		@Override
		protected void onPreExecute() {
			super.onPreExecute();
			pDialog = new ProgressDialog(getActivity());
			pDialog.setMessage("Submitting listing..");
			pDialog.setIndeterminate(false);
			pDialog.setCancelable(true);
			pDialog.show();
		}
		
		/**
		 * Creating product
		 * */
		protected String doInBackground(String... args) {
			
			String userloggedin = "2";
			MainActivity main = (MainActivity) getActivity();
	        LoginDatabaseHandler db = main.getDbHandler();
	       
	        if(db.isLoggedIn()){
	        	userloggedin = String.valueOf(db.getID());
	        	
	        }else{
	        	userloggedin = "2";
	        	
	        }
			String description = inputDescription.getText().toString();
			String asking_price = inputAskingPrice.getText().toString();
			String other_offer = inputOtherOffer.getText().toString();
			String title = inputTitle.getText().toString();
			
			// Building Parameters to give to web service
			List<NameValuePair> params = new ArrayList<NameValuePair>();
			params.add(new BasicNameValuePair("userloggedin",userloggedin));
			params.add(new BasicNameValuePair("description",description));
			params.add(new BasicNameValuePair("asking_price",asking_price));
			params.add(new BasicNameValuePair("other_offer",other_offer));
			params.add(new BasicNameValuePair("title",title));

			// getting JSON Object
			JSONParser jsonParser = new JSONParser();
			JSONObject json = jsonParser.makeHttpRequest(url_create_product,
					"POST", params);
			
			// check log cat for response
			Log.d("Create Response", json.toString());

			// check for success tag
			try {
				int success = json.getInt(TAG_SUCCESS);

				if (success == 1) { 
					//Show SubmittedDialogFragment since insertion successful
					showSuccessDialog();
				} else {
					
					showFailureDialog();
					
				}
			} catch (JSONException e) {
				showFailureDialog();
			}

			return null;
		}

		/**
		 * After completing background task Dismiss the progress dialog
		 * **/
		protected void onPostExecute(String file_url) {
			// dismiss the dialog once done
			pDialog.dismiss();
			
		}

	}
}
