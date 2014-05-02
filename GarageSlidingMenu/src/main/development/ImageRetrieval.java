package main.development;

import java.io.IOException;
import java.io.InputStream;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;



public class ImageRetrieval {
	
	public ImageRetrieval(){
		
	}
	
	public byte[] getImage(String imagePath){
		imagePath = imagePath.replaceAll(" ", "%20");
		byte[] image = null;
		try{
			if (imagePath != null) {
				if(imagePath.startsWith("upload")){
					imagePath = "http://proj-309-07.cs.iastate.edu/" + imagePath;
				}
				DefaultHttpClient client = new DefaultHttpClient();
				HttpGet request = new HttpGet(imagePath);
				HttpResponse response = null;
				try {
					response = client.execute(request);
				} catch (ClientProtocolException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				} catch (IOException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
				HttpEntity entity = response.getEntity();
				int imageLength = (int) (entity.getContentLength());
				InputStream is2 = null;
				try {
					is2 = entity.getContent();
				} catch (IllegalStateException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				} catch (IOException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}

				image = new byte[imageLength];
				int bytesRead = 0;
				while (bytesRead < imageLength) {
					int n = 0;
					try {
						n = is2.read(image, bytesRead, imageLength - bytesRead);
					} catch (IOException e) {
						// TODO Auto-generated catch block
						e.printStackTrace();
					}
					if (n <= 0)
						; // do some error handling
					bytesRead += n;
				}
			}
			}catch(Exception e){
				e.printStackTrace();
			}
		return image;
		
	}
}
