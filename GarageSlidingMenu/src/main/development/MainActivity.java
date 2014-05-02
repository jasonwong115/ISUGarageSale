package main.development;

import home.fragments.ContactFragment;
import home.fragments.HomeFragment;
import home.fragments.HomeFragment.OnAllSelectedListener;
import home.fragments.Listing;
import home.fragments.ListingFragment;
import home.fragments.ListingFragment.OnProductSelectedListener;
import home.fragments.LoginFragment;
import home.fragments.LoginFragment.OnLoginSelectedListener;
import home.fragments.LogoutFragment;
import home.fragments.ProductFragment;
import home.fragments.RegisterFragment;
import home.fragments.TermsOfServiceFragment;
import info.androidhive.slidingmenu.R;
import info.androidhive.slidingmenu.adapter.NavDrawerListAdapter;
import info.androidhive.slidingmenu.model.LoginDatabaseHandler;
import info.androidhive.slidingmenu.model.NavDrawerItem;

import java.util.ArrayList;

import main.development.FailureDialogFragment.OnFragmentClickListenerFail;
import main.development.SubmittedDialogFragment.OnFragmentClickListener;
import search.results.fragments.CategoryFragment;
import search.results.fragments.CreateListingFragment;
import search.results.fragments.MyListingsFragment;
import search.results.fragments.MyOffersFragment;
import search.results.fragments.MyOffersFragment.OnOfferSelectedListener;
import search.results.fragments.Offer;
import search.results.fragments.OfferFragment;
import search.results.fragments.OffersReceivedFragment;
import search.results.fragments.SearchFragment;
import android.app.SearchManager;
import android.content.Context;
import android.content.Intent;
import android.content.res.Configuration;
import android.content.res.TypedArray;
import android.os.Bundle;
import android.support.v4.app.ActionBarDrawerToggle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentTransaction;
import android.support.v4.widget.DrawerLayout;
import android.util.Log;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ListView;
import android.widget.SearchView;

/**
 * Main activity handling most fragments of the application This is also where
 * the navigation drawer is handled
 * 
 * @author jasonwong
 * 
 */
public class MainActivity extends FragmentActivity implements
		OnProductSelectedListener, OnLoginSelectedListener,
		OnFragmentClickListener, OnFragmentClickListenerFail,
		OnOfferSelectedListener, OnAllSelectedListener {

	// Variables needed to keep track of the navigation drawer
	private DrawerLayout mDrawerLayout;
	private ListView mDrawerList;
	private ActionBarDrawerToggle mDrawerToggle;

	// Keeps track of current fragment currently on
	private int currentFragment;

	Bundle extras = null;

	// nav drawer title
	private CharSequence mDrawerTitle;

	// used to store app title
	private CharSequence mTitle;

	// slide menu items
	private TypedArray navMenuIcons;

	private ArrayList<NavDrawerItem> navDrawerItems;
	private NavDrawerListAdapter adapter;
	private LoginDatabaseHandler dbHandler;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		extras = getIntent().getExtras();

		setContentView(R.layout.activity_main);

		// get login info
		dbHandler = new LoginDatabaseHandler(this);

		mTitle = mDrawerTitle = getTitle();

		// nav drawer icons from resources
		navMenuIcons = getResources()
				.obtainTypedArray(R.array.nav_drawer_icons);

		mDrawerLayout = (DrawerLayout) findViewById(R.id.drawer_layout);
		mDrawerList = (ListView) findViewById(R.id.list_slidermenu);

		navDrawerItems = new ArrayList<NavDrawerItem>();

		// adding nav drawer items to array

		// Show logged out options
		if (!dbHandler.isLoggedIn()) {
			navDrawerItems.add(new NavDrawerItem("Home", navMenuIcons
					.getResourceId(0, -1)));
			navDrawerItems.add(new NavDrawerItem("Login", navMenuIcons
					.getResourceId(1, -1)));
			navDrawerItems.add(new NavDrawerItem("Register", navMenuIcons
					.getResourceId(2, -1)));
			navDrawerItems.add(new NavDrawerItem("Report/Contact", navMenuIcons
					.getResourceId(3, -1)));
			navDrawerItems.add(new NavDrawerItem("Terms of Service",
					navMenuIcons.getResourceId(4, -1)));
			// Show logged in options
		} else {
			navDrawerItems.add(new NavDrawerItem("Home", navMenuIcons
					.getResourceId(0, -1)));
			navDrawerItems.add(new NavDrawerItem("Logout", navMenuIcons
					.getResourceId(1, -1)));
			navDrawerItems.add(new NavDrawerItem("Create Listing", navMenuIcons
					.getResourceId(3, -1)));
			navDrawerItems.add(new NavDrawerItem("My Listings", navMenuIcons
					.getResourceId(4, -1)));
			navDrawerItems.add(new NavDrawerItem("My Offers", navMenuIcons
					.getResourceId(5, -1)));
			navDrawerItems.add(new NavDrawerItem("Offers Received",
					navMenuIcons.getResourceId(6, -1)));
			navDrawerItems.add(new NavDrawerItem("Report/Contact", navMenuIcons
					.getResourceId(7, -1)));
			navDrawerItems.add(new NavDrawerItem("Terms of Service",
					navMenuIcons.getResourceId(8, -1)));
		}

		// Recycle the typed array
		navMenuIcons.recycle();

		mDrawerList.setOnItemClickListener(new SlideMenuClickListener());

		// setting the nav drawer list adapter
		adapter = new NavDrawerListAdapter(getApplicationContext(),
				navDrawerItems);
		mDrawerList.setAdapter(adapter);

		// enabling action bar app icon and behaving it as toggle button
		getActionBar().setDisplayHomeAsUpEnabled(true);
		getActionBar().setHomeButtonEnabled(true);

		mDrawerToggle = new ActionBarDrawerToggle(this, mDrawerLayout,
				R.drawable.ic_drawer, // nav menu toggle icon
				R.string.app_name, // nav drawer open - description for
									// accessibility
				R.string.app_name // nav drawer close - description for
									// accessibility
		) {
			public void onDrawerClosed(View view) {
				getActionBar().setTitle(mTitle);
				// calling onPrepareOptionsMenu() to show action bar icons
				invalidateOptionsMenu();
			}

			public void onDrawerOpened(View drawerView) {
				getActionBar().setTitle(mDrawerTitle);
				// calling onPrepareOptionsMenu() to hide action bar icons
				invalidateOptionsMenu();
			}
		};

		mDrawerLayout.setDrawerListener(mDrawerToggle);
		// If there is no past information on which fragment used last, default
		// to the home fragment
		if (savedInstanceState == null) {
			// on first time display view for first nav item
			displayView(0);
			currentFragment = 0;
		}

		// Show search results if a search was just done
		if (getIntent().getExtras() != null
				&& getIntent().getExtras().containsKey("auto")) {
			displayView(8);
			currentFragment = 8;
			// Auto used so that search results show up automatically on first
			// creation
			getIntent().removeExtra("auto");
		}

	}

	/**
	 * Used to grab the database handler
	 * 
	 * @return database handler used to hold information of user
	 */
	public LoginDatabaseHandler getDbHandler() {
		return dbHandler;
	}

	/**
	 * Slide menu item click listener
	 * */
	private class SlideMenuClickListener implements
			ListView.OnItemClickListener {
		@Override
		public void onItemClick(AdapterView<?> parent, View view, int position,
				long id) {
			// display view for selected nav drawer item
			displayView(position);
		}
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// getMenuInflater().inflate(R.menu.main, menu);
		MenuInflater inflater = getMenuInflater();
		inflater.inflate(R.menu.main, menu);

		// Associate searchable configuration with the SearchView
		SearchManager searchManager = (SearchManager) getSystemService(Context.SEARCH_SERVICE);
		SearchView searchView = (SearchView) menu.findItem(R.id.action_search)
				.getActionView();
		searchView.setSearchableInfo(searchManager
				.getSearchableInfo(getComponentName()));

		return super.onCreateOptionsMenu(menu);
	}

	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		// toggle nav drawer on selecting action bar app icon/title
		if (mDrawerToggle.onOptionsItemSelected(item)) {
			return true;
		}
		// Handle action bar actions click
		switch (item.getItemId()) {
		case R.id.action_settings:
			return true;
		case R.id.action_search:
			return true;
		default:
			return super.onOptionsItemSelected(item);
		}
	}

	/* *
	 * Called when invalidateOptionsMenu() is triggered
	 */
	@Override
	public boolean onPrepareOptionsMenu(Menu menu) {
		// if nav drawer is opened, hide the action items
		boolean drawerOpen = mDrawerLayout.isDrawerOpen(mDrawerList);
		menu.findItem(R.id.action_settings).setVisible(!drawerOpen);
		return super.onPrepareOptionsMenu(menu);
	}

	/**
	 * Diplaying fragment view for selected nav drawer list item
	 * */
	private void displayView(int position) {
		
		// update the main content by replacing fragments
		currentFragment = position;
		//Default title for fragments
		String fragmentTitle = "ISU Garage Sale";
		Fragment fragment = null;
		
		//If the user is logged in, show correct fragments on click
		if (dbHandler.isLoggedIn()) {
			switch (position) {
			case 0:
				fragment = new HomeFragment();
				fragmentTitle = "Home";
				break;
			case 1:
				if (dbHandler.isLoggedIn()) {
					fragment = new LogoutFragment();
					fragmentTitle = "Logout";
				} else {
					fragment = new LoginFragment();
					fragmentTitle = "Login";
				}
				break;
			case 2:
				fragment = new CreateListingFragment();
				fragmentTitle = "Create Listing";

				break;
			case 3:
				fragment = new MyListingsFragment();
				fragmentTitle = "My Listings";
				break;
			case 4:
				fragment = new MyOffersFragment();
				fragmentTitle = "My Offers";
				break;
			case 5:
				fragment = new OffersReceivedFragment();
				fragmentTitle = "Offers Received";
				break;
			case 6:
				fragment = new ContactFragment();
				fragmentTitle = "Contact Us";
				break;
			case 7:
				fragment = new TermsOfServiceFragment();
				fragmentTitle = "Terms of Service";
				break;
			case 8:
				//Check whether the homefragment used or search widget used
				if (getIntent().getExtras().containsKey("searchCategory")) {
					fragment = new CategoryFragment();
				} else {
					fragment = new SearchFragment();
				}
				fragmentTitle = "Search Results";
				break;

			case 9:
				fragment = new ListingFragment();
				fragmentTitle = "Search Results";
				break;
			default:
				break;
			}
			
		//If the user is not logged in, show correct fragments on click
		} else {
			switch (position) {
			case 0:
				fragment = new HomeFragment();
				fragmentTitle = "Home";
				break;
			case 1:
				fragment = new LoginFragment();
				fragmentTitle = "Login";
				break;
			case 2:
				fragment = new RegisterFragment();
				fragmentTitle = "Register";
				break;
			case 3:
				fragment = new ContactFragment();
				fragmentTitle = "Contact Us";
				break;
			case 4:
				fragment = new TermsOfServiceFragment();
				fragmentTitle = "Terms of Service";
				break;
			case 8:
				//Check whether the homefragment used or search widget used
				if (getIntent().getExtras().containsKey("searchCategory")) {
					fragment = new CategoryFragment();
				} else {
					fragment = new SearchFragment();
				}
				fragmentTitle = "Search Results";
				break;

			case 9:
				fragment = new ListingFragment();
				fragmentTitle = "Search Results";
				break;
			default:
				break;
			}
		}
		
		//Create and show the fragment, using correct title, etc
		if (fragment != null) {
			FragmentManager fragmentManager = getSupportFragmentManager();
			FragmentTransaction fragmentTransaction = fragmentManager
					.beginTransaction();
			fragmentTransaction.addToBackStack(null);
			fragmentTransaction.replace(R.id.frame_container, fragment)
					.commit();

			// update selected item and title, then close the drawer
			mDrawerList.setItemChecked(position, true);
			mDrawerList.setSelection(position);
			setTitle(fragmentTitle);
			mDrawerLayout.closeDrawer(mDrawerList);
		} else {
			// error in creating fragment
			Log.e("MainActivity", "Error in creating fragment");
		}

	}
	/**
	 * Set title of the fragment to be shown in action bar
	 */
	@Override
	public void setTitle(CharSequence title) {
		mTitle = title;
		getActionBar().setTitle(mTitle);
	}

	/**
	 * When using the ActionBarDrawerToggle, you must call it during
	 * onPostCreate() and onConfigurationChanged()...
	 */

	@Override
	protected void onPostCreate(Bundle savedInstanceState) {
		super.onPostCreate(savedInstanceState);
		// Sync the toggle state after onRestoreInstanceState has occurred.
		mDrawerToggle.syncState();
	}

	@Override
	public void onConfigurationChanged(Configuration newConfig) {
		super.onConfigurationChanged(newConfig);
		// Pass any configuration change to the drawer toggls
		mDrawerToggle.onConfigurationChanged(newConfig);
	}

	/**
	 * Used for the dialog fragments
	 */
	@Override
	public void onFragmentClick(int action, Object object) {
		switch (action) {
		case 1:
			// Go back to the home fragment but make sure to "refresh" data
			Intent i = new Intent(getApplicationContext(), MainActivity.class);
			startActivity(i);

			break;
		case 2:
			//Stay on current page
			displayView(currentFragment); 
			break;
		}
	}

	/**
	 * Used for fragments related to listings (my listings, search features, etc)
	 */
	@Override
	public void onProductSelected(Listing product) {
		// TODO Auto-generated method stub
		ProductFragment fragment = new ProductFragment();
		FragmentManager fragmentManager = getSupportFragmentManager();
		FragmentTransaction fragmentTransaction = fragmentManager
				.beginTransaction();
		fragment.loadProduct(fragmentTransaction, product);
		fragmentTransaction.addToBackStack(null);

		fragmentTransaction.replace(R.id.frame_container, fragment).commit();
	}

	/**
	 * Used for the homeFragment
	 */
	@Override
	public void onAllSelected() {
		displayView(9);
	}

	/**
	 * Used for the LoginFragment
	 */
	@Override
	public void onLoginSelected() {
		Intent i = new Intent(getApplicationContext(), MainActivity.class);
		startActivity(i);
	}

	/**
	 * Used for fragments related to offers (my offers, offers received)
	 */
	@Override
	public void onOfferSelected(Offer offer) {
		// TODO Auto-generated method stub
		OfferFragment fragment = new OfferFragment();
		FragmentManager fragmentManager = getSupportFragmentManager();
		FragmentTransaction fragmentTransaction = fragmentManager
				.beginTransaction();
		fragment.loadOffer(fragmentTransaction, offer);
		fragmentTransaction.addToBackStack(null);

		fragmentTransaction.replace(R.id.frame_container, fragment).commit();
	}

}
