package home.fragments;

import info.androidhive.slidingmenu.R;

import java.text.NumberFormat;
import java.util.ArrayList;
import java.util.Locale;

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

import com.fedorvlasov.lazylist.ImageLoader;

public class ProductListAdapter extends ArrayAdapter<Listing>{
	
	private Activity activity;
    private static LayoutInflater inflater=null;
    public ImageLoader imageLoader; 
	public ArrayList<Listing> listings;
	
	public ProductListAdapter(Context context, int textViewResourceId,ArrayList<Listing> objects) {
		super(context, textViewResourceId, objects);
		listings = objects;
		inflater = LayoutInflater.from(context);
		imageLoader = new ImageLoader(context);
	}
	
	@Override
	public int getCount() {
		return listings.size();
	}


	@Override
	public long getItemId(int i) {
		return i;
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		View vi=convertView;
        if(convertView==null){
            vi = inflater.inflate(R.layout.list_row, null);
        }

        Listing listing = listings.get(position);
        listing.productNameField = (TextView)vi.findViewById(R.id.row_product_name);
        listing.descriptionField = (TextView)vi.findViewById(R.id.description); // artist name
        listing.priceField = (TextView)vi.findViewById(R.id.price); // duration
        listing.thumb_imageField = (ImageView)vi.findViewById(R.id.list_image); // thumb image
        
        // Setting all values in listview
        listing.context = getContext();
        listing.productNameField.setText(listing.getTitle());
        listing.descriptionField.setText(listing.getDescription());
        Locale locale = new Locale("en", "US");
        NumberFormat fmt = NumberFormat.getCurrencyInstance(locale);
        listing.priceField.setText(String.valueOf(fmt.format(Double.valueOf(listing.getAskingPrice()))));
        if(listing.getImageBytes() != null){
        	Bitmap bMap = BitmapFactory.decodeByteArray(listing.getImageBytes(), 0, listing.getImageBytes().length);
        	listing.thumb_imageField.setImageBitmap(bMap);
        }
        return vi;
	}

}
