package main.development;

import android.app.Activity;
import android.app.SearchManager;
import android.content.Intent;
import android.os.Bundle;

/**
 * This activity informs the main activity whenever the search bar widget is
 * used The main activity then uses a searchfragment to display the search
 * results
 * 
 * @author jasonwong
 * 
 */
public class SearchResultsActivity extends Activity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		
		//Get the search terms the user inputted
		String query = getIntent().getStringExtra(SearchManager.QUERY);
		
		//Create main activity and pass it the search terms
		Intent i = new Intent(getApplicationContext(), MainActivity.class);
		i.putExtra("search", query);
		i.putExtra("auto", query);
		startActivity(i);
	}
}
