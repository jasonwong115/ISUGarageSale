package search.results.fragments;


import info.androidhive.slidingmenu.R;
import info.androidhive.slidingmenu.model.LoginDatabaseHandler;

import java.io.IOException;
import java.io.InputStream;
import java.util.ArrayList;
import java.util.List;
import java.util.concurrent.ExecutionException;

import main.development.FailureDialogFragment;
import main.development.ImageRetrieval;
import main.development.JSONParser;
import main.development.MainActivity;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import search.results.fragments.MyOffersFragment.OnOfferSelectedListener;
import android.app.Activity;
import android.app.ProgressDialog;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v4.app.DialogFragment;
import android.support.v4.app.FragmentTransaction;
import android.support.v4.app.ListFragment;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ListView;

/**
 * 
 * @author jasonwong
 * Fragment displays all offers the user has received from other users
 */
public class OffersReceivedFragment extends ListFragment{
	
	OnOfferSelectedListener listener;
	
	//Useful variables
	private static String url_create_product = "webservice/retrieve_offers_received.php";
	private static final String TAG_SUCCESS = "success";
	private static final String TAG_OFFERS = "offers";
	
	ProgressDialog pDialog;
	View rootView;
	
    //ArrayList of all offers
    private ArrayList<Offer> offersList;
	
    //Constructor does nothing
	public OffersReceivedFragment(){}
	

	@Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
            Bundle savedInstanceState) {
		//Setup view
        rootView = inflater.inflate(R.layout.fragment_offers, container, false);
        ListView list = (ListView) rootView.findViewById(android.R.id.list);
    	offersList = null;
    	
    	//Get all offers
        RetrieveOffers offers = new RetrieveOffers();
        offers.execute();
        
        try {
       	offersList = offers.get();
       	} catch (InterruptedException e) {
       		showFailureDialog();
       	} catch (ExecutionException e) {
       		showFailureDialog();
       	} // End of catch
        
        //Setup view with offers
        OfferListAdapter adapter = new OfferListAdapter(rootView.getContext(),android.R.id.list,offersList);
   	 	list.setAdapter(adapter);
        return rootView;
    }
	
	/*
	 * Dialog to show if listing has not been successfully submitted to the database
	 */
	public void showFailureDialog() {
		FragmentTransaction fm = getActivity().getSupportFragmentManager().beginTransaction();
		DialogFragment dialog = FailureDialogFragment.newInstance("Error occurred trying to retrieve offers!");
        // Create an instance of the dialog fragment and show it
        dialog.show(fm, "FailureDialogFragment");
    }
	
	/**
	 * Background Async Task to retrieve offers the user has received
	 * */
	class RetrieveOffers extends AsyncTask<Void, Integer, ArrayList<Offer>> {
		
		/**
		 * Before starting background thread Show Progress Dialog
		 * */
		@Override
		protected void onPreExecute() {
			super.onPreExecute();
			pDialog = new ProgressDialog(getActivity());
			pDialog.setMessage("Submitting info..");
			pDialog.setIndeterminate(false);
			pDialog.setCancelable(true);
			pDialog.show();
		} // End of onPreExecute
		/**
		 * Creating product
		 * */
		protected ArrayList<Offer> doInBackground(Void... args) {
			String userloggedin = "null";
			MainActivity main = (MainActivity) getActivity();
	        LoginDatabaseHandler db = main.getDbHandler();
	       
	        if(db.isLoggedIn()){
	        	userloggedin = String.valueOf(db.getID());
	        	
	        }else{
	        	
	        }
			Log.d(userloggedin,userloggedin);
			// Building Parameters
			List<NameValuePair> params = new ArrayList<NameValuePair>();
			params.add(new BasicNameValuePair("userloggedin", userloggedin));
			ArrayList<Offer> offerList = new ArrayList<Offer>();
			// getting JSON Object
			JSONParser jsonParser = new JSONParser();
			JSONObject json = jsonParser.makeHttpRequest(url_create_product,
					"POST", params);
			if(json == null){
				//showFailureDialog();
				return offerList;
			}
			
			// check log cat fro response
			Log.d("Create Response", json.toString());

			// products JSONArray returned by web service
		    JSONArray offers = null;
		    
			// check for success tag
			try {
				int success = json.getInt(TAG_SUCCESS);

				if (success==1) {
					offers = json.getJSONArray(TAG_OFFERS);
					
                    // looping through All offers
                    for (int i = 0; i < offers.length(); i++) {
                        JSONObject c = offers.getJSONObject(i);
                        int sellerID = c.getInt("sellerID");
            			int buyerID = c.getInt("buyerID");
            			int listingID = c.getInt("listingID");
            			int offerID = c.getInt("offerID");
            			String productName = c.getString("productName");
            			int offerStatus = c.getInt("offerStatus");
            			String offerComment = c.getString("offerComment");
            			double offerPrice = c.getDouble("offerPrice");
            			String otherOffer = c.getString("otherOffer");
            			String imagePath = c.getString("imagePath");
            			int accepted = c.getInt("accepted");
            			int best_offer = c.getInt("best_offer");
            			
            			Offer o = new Offer(offerID,sellerID, buyerID, listingID, productName,
            					offerStatus, offerComment,
            					offerPrice, otherOffer,imagePath,accepted,best_offer);
            			if (imagePath != null) {
            				ImageRetrieval ir = new ImageRetrieval();
            				byte[] image = ir.getImage(imagePath);
            				o.setImageBytes(image);
            			}
            			offerList.add(o);
                    } // End of for
				} // End of if
			} catch (JSONException e) {
				showFailureDialog();
			} // End of catch
			return offerList;
		} // End of doInBackground
		protected void onPostExecute(ArrayList<Offer> retreivedList) {
			// dismiss the dialog once done
			pDialog.dismiss();
			
		} // End of onPostExecute
	} // End of AsyncTask
	
	//Executed for click listener from main activity
	public void onListItemClick(ListView l, View v, int position, long id) {
		  listener.onOfferSelected(offersList.get(position));
	}
	
	@Override
    public void onAttach(Activity activity) {
      super.onAttach(activity);
      if (activity instanceof OnOfferSelectedListener) {
        listener = (OnOfferSelectedListener) activity;
      } else {
        throw new ClassCastException(activity.toString()
            + " must implemenet MyOffersFragment.OnOffersSelectedListener");
      } // End of else
    } // End of onAttach
} // End of class
