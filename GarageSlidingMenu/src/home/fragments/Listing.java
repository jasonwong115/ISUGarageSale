package home.fragments;

import android.content.Context;
import android.widget.ImageView;
import android.widget.TextView;

/**
 * 
 * This class represents a product listing. 
 *
 */


public class Listing{
	private int id;
	private int userId;
	private String username;
	private String description;
	private String askingPrice;
	private String otherOffer;
	private String productName;
	private String dateCreated;
	private int status;
	private int categoryId;
	private String imagePath;
	private String keywords;
	private byte[] imageBytes;
	private int reviewed;
	private int amazonProductId;
	private int bestOfferId;
	private int acceptedOfferId;
	
	



	public transient Context context;
	public transient TextView productNameField;// title
    public transient TextView descriptionField; // artist name
    public transient TextView priceField; // duration
    public transient ImageView thumb_imageField; // thumb image

	
	public Listing(int id, int userId,String username, String description, String askingPrice,
			String otherOffer, int reviewed, String productName,
			String dateCreated, int status, int categoryId, String imagePath,
			String keywords, int amazonProductId, int bestOfferId, int acceptedOfferId) {
		super();
		this.id = id;
		this.userId = userId;
		this.username = username;
		this.description = description;
		this.askingPrice = askingPrice;
		this.otherOffer = otherOffer;
		this.productName = productName;
		this.dateCreated = dateCreated;
		this.status = status;
		this.categoryId = categoryId;
		this.imagePath = imagePath;
		this.keywords = keywords;
		this.imageBytes = null;
		this.reviewed = reviewed;
		this.amazonProductId = amazonProductId;
		this.bestOfferId = bestOfferId;
		this.acceptedOfferId = acceptedOfferId;
	}
	

	public int getId() {
		return id;
	}
	public void setId(int id) {
		this.id = id;
	}
	public int getUserId() {
		return userId;
	}
	public void setUserId(int userId) {
		this.userId = userId;
	}
	public String getUsername() {
		return username;
	}
	public void setUsername(String username) {
		this.username = username;
	}
	public String getDescription() {
		return description;
	}
	public void setDescription(String description) {
		this.description = description;
	}
	public String getAskingPrice() {
		return askingPrice;
	}
	public void setAskingPrice(String askingPrice) {
		this.askingPrice = askingPrice;
	}
	public String getOtherOffer() {
		return otherOffer;
	}
	public void setOtherOffer(String otherOffer) {
		this.otherOffer = otherOffer;
	}
	public String getTitle() {
		return productName;
	}
	public void setTitle(String productName) {
		this.productName = productName;
	}
	public String getDateCreated() {
		return dateCreated;
	}
	public void setDateCreated(String dateCreated) {
		this.dateCreated = dateCreated;
	}
	public int getStatus() {
		return status;
	}
	public void setStatus(int status) {
		this.status = status;
	}
	public int getCategoryId() {
		return categoryId;
	}
	public void setCategoryId(int categoryId) {
		this.categoryId = categoryId;
	}
	public String getImagePath() {
		return imagePath;
	}
	public void setImagePath(String imagePath) {
		this.imagePath = imagePath;
	}
	public String getKeywords() {
		return keywords;
	}
	public void setKeywords(String keywords) {
		this.keywords = keywords;
	}
	public byte[] getImageBytes() {
		return imageBytes;
	}
	public int getReviewed() {
		return reviewed;
	}
	public void setReviewed(int reviewed) {
		this.reviewed = reviewed;
	}
	public int getAmazonProductId() {
		return amazonProductId;
	}
	public void setAmazonProductId(int amazonProductId) {
		this.amazonProductId = amazonProductId;
	}
	public int getBestOfferId() {
		return bestOfferId;
	}
	public void setBestOfferId(int bestOfferId) {
		this.bestOfferId = bestOfferId;
	}
	public int getAcceptedOfferId() {
		return acceptedOfferId;
	}
	public void setAcceptedOfferId(int acceptedOfferId) {
		this.acceptedOfferId = acceptedOfferId;
	}
	public void setImageBytes(byte[] imageBytes) {
		this.imageBytes = imageBytes;
	}


	

	
	
}
