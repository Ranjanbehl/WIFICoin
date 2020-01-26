package online.wificoin.wificoin;

import android.app.DownloadManager;
import android.content.Context;
import android.util.Log;
import android.webkit.JavascriptInterface;

import com.android.volley.NetworkResponse;
import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.JsonRequest;
import com.android.volley.toolbox.Volley;

import org.json.JSONException;
import org.json.JSONObject;

public class JavaScriptInterface {
    Context mContext;

    public String IMEI = "";
    public double lon = 0.0;
    public double lat = 0.0;
    public double alt = 0.0;
    public Object balance;
    public String wifidata;

    JavaScriptInterface(Context c, double lo, double la, double at, String IEI) {
        mContext = c;
        lon = lo;
        lat = la;
        alt = at;
        IMEI = IEI;
        balance = (Object) "Loading...";
    }
    @JavascriptInterface
    public double getLon() {
        return lon;
    }

    @JavascriptInterface
    public double getLat() {
        return lat;
    }

    @JavascriptInterface
    public double getAlt() {
        return alt;
    }

    @JavascriptInterface
    public String getIMEI() {
        return IMEI;
    }

    @JavascriptInterface
    public String getBalance() {
        return balance.toString();
    }

    @JavascriptInterface
    public void updateLoc(double la, double lo, double at) {
        lon = lo;
        lat = la;
        alt = at;
    }

    @JavascriptInterface
    public void auth() {
        RequestQueue queue = Volley.newRequestQueue(mContext);
        String url = "http://wificoin.eu5.net/api.php";
        JSONObject reqobj = new JSONObject();
        try {
            reqobj.put("TYPE", "AUTH");
            reqobj.put("IMEI", IMEI);
        } catch (JSONException je) {

        }

        JsonObjectRequest jsonRequest = new JsonObjectRequest(Request.Method.POST, url, reqobj, new Response.Listener<JSONObject>() {
            @Override
            public void onResponse(JSONObject response) {
                Log.d("Req success", "Success");
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
                Log.e("Req fail", error.toString());
            }
        });
        queue.add(jsonRequest);
    }

    @JavascriptInterface
    public void balanceq() {
        RequestQueue queue = Volley.newRequestQueue(mContext);
        String url = "http://wificoin.eu5.net/api.php";
        JSONObject reqobj = new JSONObject();
        try {
            reqobj.put("TYPE", "BALANCEQ");
            reqobj.put("IMEI", IMEI);
        } catch (JSONException je) {

        }

        JsonObjectRequest jsonRequest = new JsonObjectRequest(Request.Method.POST, url, reqobj, new Response.Listener<JSONObject>() {
            @Override
            public void onResponse(JSONObject response) {
                Log.d("Req success", "Success");
                try {
                    balance = response.get("BALANCE");
                    Log.d("balance", balance.toString());
                } catch (JSONException je) {

                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
                Log.e("Req fail", error.toString());
            }
        });
        queue.add(jsonRequest);
    }

    @JavascriptInterface
    public void addWifi(String ssid, String password) {

        RequestQueue queue = Volley.newRequestQueue(mContext);
        String url = "http://wificoin.eu5.net/api.php";
        JSONObject reqobj = new JSONObject();
        JSONObject wifiobj = new JSONObject();
        JSONObject coordobj = new JSONObject();
        try {
            reqobj.put("TYPE", "EDIT");
            reqobj.put("IMEI", IMEI);
            wifiobj.put("SSID", ssid);
            wifiobj.put("PASSWORD", password);
            reqobj.put("WIFI", wifiobj);
            coordobj.put("LON", lon);
            coordobj.put("LAT", lat);
            coordobj.put("ALT", alt);
            reqobj.put("COORD", coordobj);
        } catch (JSONException je) {

        }

        Log.e("addwifi", reqobj.toString());


        JsonObjectRequest jsonRequest = new JsonObjectRequest(Request.Method.POST, url, reqobj, new Response.Listener<JSONObject>() {
            @Override
            public void onResponse(JSONObject response) {
                Log.d("Req success", "Success");
                try {
                    balance = response.get("BALANCE");
                    Log.d("balance", balance.toString());
                } catch (JSONException je) {

                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
                Log.e("Req fail", error.toString());
            }
        });
        queue.add(jsonRequest);
    }

    @JavascriptInterface
    public void loadWifi() {
        RequestQueue queue = Volley.newRequestQueue(mContext);
        String url = "http://wificoin.eu5.net/api.php";
        JSONObject reqobj = new JSONObject();
        JSONObject coordobj = new JSONObject();
        try {
            reqobj.put("TYPE", "LOAD");
            reqobj.put("IMEI", IMEI);
            coordobj.put("LON", lon);
            coordobj.put("LAT", lat);
            coordobj.put("ALT", alt);
            reqobj.put("COORD", coordobj);
            reqobj.put("RAD", 100);
        } catch (JSONException je) {

        }

        Log.d("Load WIFI", reqobj.toString());

        JsonObjectRequest jsonRequest = new JsonObjectRequest(Request.Method.POST, url, reqobj, new Response.Listener<JSONObject>() {
            @Override
            public void onResponse(JSONObject response) {
                Log.d("Req success", "Success");
                try {
                    wifidata = response.getString("WIFIS");
                } catch (JSONException je) {

                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
                Log.e("Req fail", error.toString());
            }
        });
        queue.add(jsonRequest);
    }

    @JavascriptInterface
    public String getMapData() {
        Log.d("POLL WIFI", wifidata);
        return wifidata;
    }


}