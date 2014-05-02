package search.results.fragments;


import home.fragments.Listing;
import home.fragments.ListingFragment.OnProductSelectedListener;
import home.fragments.ProductListAdapter;
import info.androidhive.slidingmenu.R;
import info.androidhive.slidingmenu.model.LoginDatabaseHandler;

import java.io.IOException;
import java.io.InputStream;
import java.sql.Date;
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
 * Fragment displays all the listings the user is currently selling
 */
public class MyListingsFragment extends ListFragment{
	
	
	OnProductSelectedListener listener;
	
	//Useful variables
	private static String url_create_product = "webservice/retrieve_my_listings.php";
	private static final String TAG_SUCCESS = "success";
	private static final String TAG_LISTINGS = "listings";
	
	ProgressDialog pDialog;
	View rootView;
    MainActivity main;
    
    //ArrayList of all listings
    private ArrayList<Listing> listingsList;
	
    //Constructor does nothing
	public MyListingsFragment(){}
	

	@Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
            Bundle savedInstanceState) {
		//Setup view
        rootView = inflater.inflate(R.layout.fragment_listings, container, false);
        ListView list = (ListView) rootView.findViewById(android.R.id.list);
        //ArrayList of all listings
        listingsList = null;
    	main = (MainActivity) getActivity();
    	//Retrieve Listings user is currently selling
        RetrieveListings listings = new RetrieveListings();
        listings.execute();
        
        try {
       	listingsList = listings.get();
       	} catch (InterruptedException e) {
       		showFailureDialog();
       	} catch (ExecutionException e) {
       		showFailureDialog();
       	}
        
        //Setup view with loaded listings
        ProductListAdapter adapter = new ProductListAdapter(rootView.getContext(),android.R.id.list,listingsList);
   	 	list.setAdapter(adapter);
        return rootView;
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
	 * Background Async Task to retrieve listings currently selling
	 * */
	class RetrieveListings extends AsyncTask<Void, Integer, ArrayList<Listing>> {
		
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
		protected ArrayList<Listing> doInBackground(Void... args) {
			
			String userloggedin = "null";
			MainActivity main = (MainActivity) getActivity();
	        LoginDatabaseHandler db = main.getDbHandler();
	       
	        if(db.isLoggedIn()){
	        	userloggedin = String.valueOf(db.getID());
	        	
	        }else{
	        	
	        }
			
			// Building Parameters
			List<NameValuePair> params = new ArrayList<NameValuePair>();
			params.add(new BasicNameValuePair("userloggedin", userloggedin));
	
			//ArrayList to hold all listings
			ArrayList<Listing> listingList = new ArrayList<Listing>();
			
			
			// getting JSON Object
			JSONParser jsonParser = new JSONParser();
			JSONObject json = jsonParser.makeHttpRequest(url_create_product,
					"POST", params);
			if(json == null){
				showFailureDialog();
				return listingList;
			}
			
			// check log cat fro response
			Log.d("Create Response", json.toString());
			
			//JSON Array of listings returned by web service
		    JSONArray listings = null;

			// check for success tag
			try {
				int success = json.getInt(TAG_SUCCESS);

				if (success==1) {
					listings = json.getJSONArray(TAG_LISTINGS);
					
                    // looping through All Products
                    for (int i = 0; i < listings.length(); i++) {
                        JSONObject c = listings.getJSONObject(i);
                        int userid = c.getInt("userid");
                        String username = c.getString("handle");
            			int listingid = c.getInt("listingid");
            			String productName = c.getString("productName");
            			String description = c.getString("description");
            			String askingPrice = c.getString("asking_price");
            			String image_paths = c.getString("image_paths");
            			int status = 0;
            			String other_offer = "test";
            			int reviewed = 0;
            			String date_created = null;
            			String keywords = "test";
            			int categoryid = 0;
            			int amazon_productid = 0;
						int best_offerid = 0;
						int accepted_offerid = 0;
						
            			Listing l = new Listing(listingid, userid, username, description, askingPrice,
            					other_offer, reviewed, productName, date_created, status,
            					categoryid, null, keywords, amazon_productid, best_offerid, accepted_offerid);
            			if (image_paths != null) {
            				ImageRetrieval ir = new ImageRetrieval();
            				byte[] image = ir.getImage(image_paths);
            				l.setImageBytes(image);
            			}
            			listingList.add(l);
                    }
				}
			} catch (JSONException e) {
				showFailureDialog();
			}
			return listingList;
		}
		protected void onPostExecute(ArrayList<Listing> retreivedList) {
			// dismiss the dialog once done
			pDialog.dismiss();
			
		} // End of onPostExecute
	} // End of AsyncTask
	
	/**
	 * Executed when a listing is clicked
	 */
	public void onListItemClick(ListView l, View v, int position, long id) {
		  listener.onProductSelected(listingsList.get(position));
	} // End of onListItemClick
	
	@Override
    public void onAttach(Activity activity) {
      super.onAttach(activity);
      if (activity instanceof OnProductSelectedListener) {
        listener = (OnProductSelectedListener) activity;
      } else {
        throw new ClassCastException(activity.toString()
            + " must implemenet MyListingsFragment.OnListingsSelectedListener");
      } // End of else
    } // End of onAttach
} // End of class
