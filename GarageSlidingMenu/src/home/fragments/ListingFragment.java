package home.fragments;

import java.io.IOException;
import java.util.ArrayList;

import java.util.concurrent.ExecutionException;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.xpath.XPath;
import javax.xml.xpath.XPathConstants;
import javax.xml.xpath.XPathExpression;
import javax.xml.xpath.XPathExpressionException;
import javax.xml.xpath.XPathFactory;

import org.w3c.dom.Document;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;
import org.xml.sax.SAXException;

import main.development.ImageRetrieval;
import info.androidhive.slidingmenu.R;
import android.annotation.TargetApi;
import android.app.Activity;
import android.app.ProgressDialog;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.support.v4.app.FragmentTransaction;
import android.support.v4.app.ListFragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ListView;


public class ListingFragment extends ListFragment {
	private ArrayList<Listing> listings;
	private OnProductSelectedListener listener;
	private ProductListAdapter adapter;
	private final String url = "http://proj-309-07.cs.iastate.edu/webservice/get_listings.php?page=";

	View rootView;
	ProgressDialog pDialog;

	public interface OnProductSelectedListener {
		public void onProductSelected(Listing product);
	}

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstanceState) {
		// View view = inflater.inflate(R.layout.product,container, false);
		rootView = inflater.inflate(R.layout.fragment_listings, container,
				false);

		ListView list = (ListView) rootView.findViewById(android.R.id.list);

		LoadListings task = (LoadListings) new LoadListings().execute(0);
		listings = null;
		try {
			listings = task.get();
		} catch (InterruptedException e) {
			e.printStackTrace();
		} catch (ExecutionException e) {
			e.printStackTrace();
		}

		adapter = new ProductListAdapter(rootView.getContext(),
				android.R.id.list, listings);
		list.setAdapter(adapter);
		list.setOnScrollListener(new EndlessScrollListener() {
			@Override
			public void onLoadMore(int page, int totalItemsCount) {
				customLoadMoreDataFromApi(page);
				adapter = new ProductListAdapter(rootView.getContext(),
						android.R.id.list, listings);
			}
		});

		return rootView;
	}

	// Append more data into the adapter
	public void customLoadMoreDataFromApi(int page) {
		// Use the offset value and add it as a parameter to your API request to
		// retrieve paginated data.
		if (page == 0) {
			LoadListings task = (LoadListings) new LoadListings().execute(0);
			listings = null;
			try {
				listings = task.get();
			} catch (InterruptedException e) {
				e.printStackTrace();
			} catch (ExecutionException e) {
				e.printStackTrace();
			}
		} else {
			LoadListings task = (LoadListings) new LoadListings().execute(page);
			ArrayList<Listing> l = null;
			try {
				l = task.get();
				listings.addAll(l);
				adapter.notifyDataSetChanged();
			} catch (InterruptedException e) {
				e.printStackTrace();
			} catch (ExecutionException e) {
				e.printStackTrace();
			}
		}
	}

	public void onListItemClick(ListView l, View v, int position, long id) {
		listener.onProductSelected(listings.get(position));
	}

	@TargetApi(Build.VERSION_CODES.HONEYCOMB)
	@Override
	public void onAttach(Activity activity) {
		super.onAttach(activity);
		if (activity instanceof OnProductSelectedListener) {
			listener = (OnProductSelectedListener) activity;
		} else {
			throw new ClassCastException(
					activity.toString()
							+ " must implemenet MyListFragment.OnProductSelectedListener");
		}
	}

	// May also be triggered from the Activity
	public void showProductDialog(int position) {
		FragmentTransaction fm = getActivity().getSupportFragmentManager()
				.beginTransaction();
		ProductFragment dialog = new ProductFragment();
		// Create an instance of the dialog fragment and show it
		dialog.loadProduct(fm, listings.get(position));
	}

	/**
	 * Background Async Task to Create new product
	 * */
	class LoadListings extends AsyncTask<Integer, Integer, ArrayList<Listing>> {

		/**
		 * Before starting background thread Show Progress Dialog
		 * */
		@Override
		protected void onPreExecute() {
			super.onPreExecute();
			pDialog = new ProgressDialog(getActivity());
			pDialog.setMessage("Retrieving Products..");
			pDialog.setIndeterminate(false);
			pDialog.setCancelable(true);
			pDialog.show();
		}

		/**
		 * Creating product
		 * */
		protected ArrayList<Listing> doInBackground(Integer... params) {

			int page = params[0];


			// InetAddress dstAddress = InetAddress.getByName(host);

			DocumentBuilderFactory builderFactory = DocumentBuilderFactory
					.newInstance();
			DocumentBuilder builder = null;
			try {
				builder = builderFactory.newDocumentBuilder();
			} catch (ParserConfigurationException e) {
				e.printStackTrace();
			}

			Document document = null;
			try {
				document = builder.parse(url + String.valueOf(page));
			} catch (SAXException e) {
				e.printStackTrace();
			} catch (IOException e) {
				e.printStackTrace();
			}

			XPath xPath = XPathFactory.newInstance().newXPath();

			int id = 0;
			int userId = 0;
			String username = null;
			String description = null;
			String askingPrice = null;
			String otherOffer = null;
			int reviewed = 0;
			String productName = null;
			String dateCreated = null;
			int status = 0;
			int categoryId = 0;
			String imagePath = null;
			String keywords = null;
			int amazonProductId = 0;
			int bestOfferId = 0;
			int acceptedOfferId = 0;
			int amazon_productid = 0;
			int best_offerid = 0;
			int accepted_offerid = 0;
			int categoryid = 0;
			ArrayList<Listing> listings = new ArrayList<Listing>();
			XPathExpression expr = null;
			try {
				expr = xPath.compile("//listings/*");
			} catch (XPathExpressionException e1) {
				e1.printStackTrace();
			}
			NodeList nodeList = null;
			try {
				nodeList = (NodeList) expr.evaluate(document,
						XPathConstants.NODESET);
			} catch (XPathExpressionException e) {
				e.printStackTrace();
			}

			for (int i = 0; i < nodeList.getLength(); i++) {
				NodeList ns = nodeList.item(i).getChildNodes();

				for (int j = 0; j < ns.getLength(); j++) {
					Node item = ns.item(j);
					String nodeName = ns.item(j).getNodeName();
					String nodeValue = null;
					try {
						nodeValue = item.getChildNodes().item(0).getNodeValue();
					} catch (Exception e) {
						nodeValue = null;
					}
					if (nodeName.equals("id")) {
						id = Integer.valueOf(nodeValue);
					} else if (nodeName.equals("userid")) {
						userId = Integer.valueOf(nodeValue);
					} else if (nodeName.equals("username")) {
						username = nodeValue;
					} else if (nodeName.equals("description")) {
						description = nodeValue;
					} else if (nodeName.equals("asking_price")) {
						askingPrice = nodeValue;
					} else if (nodeName.equals("other_offer")) {
						otherOffer = nodeValue;
					} else if (nodeName.equals("title")) {
						productName = nodeValue;
					} else if (nodeName.equals("date_created")) {
						dateCreated = nodeValue;
					} else if (nodeName.equals("status")) {
						status = Integer.valueOf(nodeValue);
					} else if (nodeName.equals("categoryid")) {
						categoryId = Integer.valueOf(nodeValue);
					} else if (nodeName.equals("image_paths")) {
						imagePath = nodeValue;
					} else if (nodeName.equals("keywords")) {
						keywords = nodeValue;
					} else if (nodeName.equals("keywords")) {
						keywords = nodeValue;
					} else if (nodeName.equals("keywords")) {
						keywords = nodeValue;
					} else if (nodeName.equals("keywords")) {
						keywords = nodeValue;
					}

				}
				Listing l = new Listing(id, userId, username, description,
						askingPrice, otherOffer, reviewed, productName,
						dateCreated, status, categoryId, imagePath, keywords,
						amazon_productid, best_offerid, accepted_offerid);

				if (imagePath != null) {
					ImageRetrieval ir = new ImageRetrieval();
					byte[] image = ir.getImage(imagePath);
					l.setImageBytes(image);
				}

				listings.add(l);
			}
			// if(page == 0){
			// ListingFragment.this.listings = listings;
			// }else{
			// ListingFragment.this.listings.addAll(listings);
			// }

			// return listings;
			return listings;
		}

		/**
		 * After completing background task Dismiss the progress dialog
		 * **/
		protected void onPostExecute(ArrayList<Listing> listing) {
			// dismiss the dialog once done
			pDialog.dismiss();

		}

	}

}
