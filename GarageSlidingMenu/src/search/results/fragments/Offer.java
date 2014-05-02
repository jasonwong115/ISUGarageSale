package search.results.fragments;

import java.io.Serializable;

import android.content.Context;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.ImageView;
import android.widget.TextView;

/**
 * This class represents an offer
 * @author Jason Wong 
 * 
 */

@SuppressWarnings("serial")
public class Offer implements Serializable, OnClickListener {
	private int offerID;
	private int sellerID;
	private int buyerID;
	private int listingID;
	private String productName;
	private int offerStatus;
	private String offerComment;
	private double offerPrice;
	private String otherOffer;
	private String imagePath;
	private byte[] imageBytes;
	private int accepted;
	private int best_offer;

	public transient Context context;
	public transient TextView productNameField;// title
	public transient TextView offerDateField; // artist name
	public transient TextView offerCommentField; // duration
	public transient TextView offerPriceField;// title
	public transient TextView otherOfferField; // artist name
	public transient ImageView thumb_imageField; // thumb image

	public Offer(int offerID, int sellerID, int buyerID, int listingID,
			String productName, int offerStatus, String offerComment,
			double offerPrice, String otherOffer, String imagePath,
			int accepted, int best_offer) {
		super();
		this.offerID = offerID;
		this.sellerID = sellerID;
		this.buyerID = buyerID;
		this.listingID = listingID;
		this.productName = productName;
		this.offerStatus = offerStatus;
		this.offerComment = offerComment;
		this.offerPrice = offerPrice;
		this.otherOffer = otherOffer;
		this.imagePath = imagePath;
		this.imageBytes = null;
		this.accepted = accepted;
		this.best_offer = best_offer;
	}

	public byte[] getImageBytes() {
		return imageBytes;
	}

	public void setImageBytes(byte[] imageBytes) {
		this.imageBytes = imageBytes;
	}

	public String getStatusName() {
		String returnStatus;
		if (accepted == 1) {
			returnStatus = "accepted";
		} else if (best_offer == 1) {
			returnStatus = "best offer";
		} else if (offerStatus == 5) {
			returnStatus = "declined";
		} else {
			returnStatus = "awaiting response";
		}
		return returnStatus;
	}

	public int getOfferID() {
		return offerID;
	}

	public int getSeller() {
		return sellerID;
	}

	public int getBuyer() {
		return buyerID;
	}

	public int getListingID() {
		return listingID;
	}

	public String getProductName() {
		return productName;
	}

	public String getOfferStatus() {
		return "ACTIVE";
	}

	public String getOfferComment() {
		return offerComment;
	}

	public double getOfferPrice() {
		return offerPrice;
	}

	public String getOtherOffer() {
		return otherOffer;
	}

	public String getImagePath() {
		return imagePath;
	}

	public void setImagePath(String imagePath) {
		this.imagePath = imagePath;
	}

	// Fields that can be clicked to load detailed offer view
	public void setOnClicks() {
		productNameField.setOnClickListener(this);
		offerDateField.setOnClickListener(this);
		offerCommentField.setOnClickListener(this);
		offerPriceField.setOnClickListener(this);
		otherOfferField.setOnClickListener(this);
		thumb_imageField.setOnClickListener(this);
	}

	@Override
	public void onClick(View v) {

	}
}
