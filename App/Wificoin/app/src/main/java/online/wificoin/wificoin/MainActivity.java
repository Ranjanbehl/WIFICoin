/*

    THIS CODE IS DEDICATED TO AND BLESSED BY ALL GROUPS AND MEMBERS OF LOVE LIVE: MUSE, AQOURS, NIJIGASAKI SCHOOL IDOL CLUB, SAINT SNOW, A-RISE

    'May all of our dreams come true' - Love Live, 2010.

    RIP HARAMBE ~ 5/31/2016


 */


package online.wificoin.wificoin;

import android.content.Context;
import android.location.Location;
import android.os.Bundle;
import android.support.annotation.NonNull;
import android.support.design.widget.BottomNavigationView;
import android.support.v7.app.AppCompatActivity;
import android.telephony.TelephonyManager;
import android.util.Log;
import android.view.MenuItem;
import android.webkit.ConsoleMessage;
import android.webkit.GeolocationPermissions;
import android.webkit.JavascriptInterface;
import android.webkit.WebChromeClient;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.TextView;

public class MainActivity extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        TelephonyManager tmm = (TelephonyManager) getSystemService(Context.TELEPHONY_SERVICE);
        String IMEI = "0";
        try {
            IMEI = tmm.getDeviceId();
        } catch (SecurityException se) {

        }
        Location loc = new Location("network");
        double lon = 0.0;
        double lat = 0.0;
        double alt = 0.0;
        lon = loc.getLongitude();
        lat = loc.getLatitude();
        alt = loc.getAltitude();

        Log.d("Alt", IMEI);

        WebView wbv = findViewById(R.id.mainweb);
        wbv.getSettings().setJavaScriptEnabled(true);
        wbv.getSettings().setAppCacheEnabled(true);
        wbv.getSettings().setDatabaseEnabled(true);
        wbv.getSettings().setDomStorageEnabled(true);
        wbv.getSettings().setAllowUniversalAccessFromFileURLs(true);
        wbv.setWebViewClient(new WebViewClient(){
            public boolean shouldOverrideUrlLoading(WebView view, String url)
            {

                view.loadUrl(url);
                return true;
            }
        });

        wbv.setWebChromeClient(new WebChromeClient() {
            public void onGeolocationPermissionsShowPrompt(String origin, GeolocationPermissions.Callback callback) {
            callback.invoke(origin, true, false);
            }
        });
        wbv.addJavascriptInterface(new JavaScriptInterface(this, lon, lat, alt, IMEI), "Wrapper");
        wbv.loadUrl("file:///android_asset/htmfiles/index.html");
    }

}
