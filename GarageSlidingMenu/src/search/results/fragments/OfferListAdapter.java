package search.results.fragments;

import info.androidhive.slidingmenu.R;

import java.text.NumberFormat;
import java.util.ArrayList;
import java.util.Locale;

import com.fedorvlasov.lazylist.ImageLoader;

import android.app.Activity;
import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.TextView;

/**
 * Adapter used to load fragment with list of offers
 * @author jasonwong
 *
 */
public class OfferListAdapter extends ArrayAdapter<Offer>{
	
	private Activity activity;
    private static LayoutInflater inflater=null;
    public ImageLoader imageLoader; 
	public ArrayList<Offer> offers;
	
	public OfferListAdapter(Context context, int textViewResourceId,ArrayList<Offer> objects) {
		super(context, textViewResourceId, objects);
		offers = objects;
		inflater = LayoutInflater.from(context);
		imageLoader = new ImageLoader(context);
	}
	
	@Override
	public int getCount() {
		return offers.size();
	}


	@Override
	public long getItemId(int i) {
		return i;
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		View vi=convertView;
        if(convertView==null){
            vi = inflater.inflate(R.layout.offer_row, null);
        }
        
        //Matching up fields with id's of layout
        Offer offer = offers.get(position);
        offer.productNameField = (TextView)vi.findViewById(R.id.offer_product_name);
        offer.offerCommentField = (TextView)vi.findViewById(R.id.comment); // artist name
        offer.offerPriceField = (TextView)vi.findViewById(R.id.offer_price); // duration
        offer.otherOfferField = (TextView)vi.findViewById(R.id.other_offer); // thumb image
        offer.thumb_imageField = (ImageView)vi.findViewById(R.id.list_image); // thumb image
        
        
        // Setting all values in listview
        offer.context = getContext();
        offer.productNameField.setText(offer.getProductName());
        offer.offerCommentField.setText("Comment: " + offer.getOfferComment());
        Locale locale = new Locale("en", "US");
        NumberFormat fmt = NumberFormat.getCurrencyInstance(locale);
        offer.offerPriceField.setText(String.valueOf(fmt.format(Double.valueOf(offer.getOfferPrice()))));
        offer.otherOfferField.setText("Offer: " + offer.getOtherOffer());
        if(offer.getImageBytes() != null){
        	//http://api.androidhive.info/music/images/eminem.png
        	//listing.getImagePath()
 
        	Bitmap bMap = BitmapFactory.decodeByteArray(offer.getImageBytes(), 0, offer.getImageBytes().length);
        	offer.thumb_imageField.setImageBitmap(bMap);

        }
        return vi;
	}

}
