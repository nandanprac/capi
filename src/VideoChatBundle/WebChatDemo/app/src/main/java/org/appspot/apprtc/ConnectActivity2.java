/*
 * libjingle
 * Copyright 2014 Google Inc.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *  1. Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *  2. Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *  3. The name of the author may not be used to endorse or promote products
 *     derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR IMPLIED
 * WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO
 * EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS;
 * OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR
 * OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
 * ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

package org.appspot.apprtc;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.util.Log;
import android.view.KeyEvent;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.inputmethod.EditorInfo;
import android.webkit.URLUtil;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageButton;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;
import java.util.Random;

/**
 * Handles the initial setup where the user selects which room to join.
 */
public class ConnectActivity2 extends Activity {
  private static final String TAG = "ConnectActivity";
  private static final int CONNECTION_REQUEST = 1;
  private static boolean commandLineRun = false;

  private ImageButton addRoomButton;
  private ImageButton removeRoomButton;
  private ImageButton connectButton;
  private ImageButton connectLoopbackButton;
  private Button answerButton;
  private EditText roomEditText;
  private ListView roomListView;
  private SharedPreferences sharedPref;
  private String keyprefVideoCallEnabled;
  private String keyprefResolution;
  private String keyprefFps;
  private String keyprefVideoBitrateType;
  private String keyprefVideoBitrateValue;
  private String keyprefVideoCodec;
  private String keyprefAudioBitrateType;
  private String keyprefAudioBitrateValue;
  private String keyprefAudioCodec;
  private String keyprefHwCodecAcceleration;
  private String keyprefCpuUsageDetection;
  private String keyprefDisplayHud;
  private String keyprefRoomServerUrl;
  private String keyprefRoom;
  private String keyprefRoomList;
  private ArrayList<String> roomList;
  private ArrayAdapter<String> adapter;
private String getID;
  @Override
  public void onCreate(Bundle savedInstanceState) {
    super.onCreate(savedInstanceState);

    // Get setting keys
    PreferenceManager.setDefaultValues(this, R.xml.preferences, false);
    sharedPref = PreferenceManager.getDefaultSharedPreferences(this);
    keyprefVideoCallEnabled = getString(R.string.pref_videocall_key);
    keyprefResolution = getString(R.string.pref_resolution_key);
    keyprefFps = getString(R.string.pref_fps_key);
    keyprefVideoBitrateType = getString(R.string.pref_startvideobitrate_key);
    keyprefVideoBitrateValue = getString(R.string.pref_startvideobitratevalue_key);
    keyprefVideoCodec = getString(R.string.pref_videocodec_key);
    keyprefHwCodecAcceleration = getString(R.string.pref_hwcodec_key);
    keyprefAudioBitrateType = getString(R.string.pref_startaudiobitrate_key);
    keyprefAudioBitrateValue = getString(R.string.pref_startaudiobitratevalue_key);
    keyprefAudioCodec = getString(R.string.pref_audiocodec_key);
    keyprefCpuUsageDetection = getString(R.string.pref_cpu_usage_detection_key);
    keyprefDisplayHud = getString(R.string.pref_displayhud_key);
    keyprefRoomServerUrl = getString(R.string.pref_room_server_url_key);
    keyprefRoom = getString(R.string.pref_room_key);
    keyprefRoomList = getString(R.string.pref_room_list_key);



      setContentView(R.layout.activity_connect);

    roomEditText = (EditText) findViewById(R.id.room_edittext);
    roomEditText.setOnEditorActionListener(
            new TextView.OnEditorActionListener() {
                @Override
                public boolean onEditorAction(
                        TextView textView, int i, KeyEvent keyEvent) {
                    if (i == EditorInfo.IME_ACTION_DONE) {
                        addRoomButton.performClick();
                        return true;
                    }
                    return false;
                }
            });
   // roomEditText.requestFocus();

    roomListView = (ListView) findViewById(R.id.room_listview);
    roomListView.setChoiceMode(ListView.CHOICE_MODE_SINGLE);

    addRoomButton = (ImageButton) findViewById(R.id.add_room_button);
    addRoomButton.setOnClickListener(addRoomListener);
    removeRoomButton = (ImageButton) findViewById(R.id.remove_room_button);
    removeRoomButton.setOnClickListener(removeRoomListener);
    connectButton = (ImageButton) findViewById(R.id.connect_button);
    connectButton.setOnClickListener(connectListener);

    answerButton=(Button) findViewById(R.id.ans_button);
    answerButton.setOnClickListener(answerListener);

    connectLoopbackButton =
        (ImageButton) findViewById(R.id.connect_loopback_button);
    connectLoopbackButton.setOnClickListener(connectListener);

    // If an implicit VIEW intent is launching the app, go directly to that URL.
    final Intent intent = getIntent();
    if ("android.intent.action.VIEW".equals(intent.getAction())
        && !commandLineRun) {
      commandLineRun = true;
      boolean loopback = intent.getBooleanExtra(
          CallActivity.EXTRA_LOOPBACK, false);
      int runTimeMs = intent.getIntExtra(
          CallActivity.EXTRA_RUNTIME, 0);
      String room = sharedPref.getString(keyprefRoom, "");
      roomEditText.setText(room);
      connectToRoom(roomEditText.getText().toString(),loopback, runTimeMs);
      return;
    }
  }

  @Override
  public boolean onCreateOptionsMenu(Menu menu) {
    getMenuInflater().inflate(R.menu.connect_menu, menu);
    return true;
  }

  @Override
  public boolean onOptionsItemSelected(MenuItem item) {
    // Handle presses on the action bar items.
    if (item.getItemId() == R.id.action_settings) {
      Intent intent = new Intent(this, SettingsActivity.class);
      startActivity(intent);
      return true;
    } else {
      return super.onOptionsItemSelected(item);
    }
  }

  @Override
  public void onPause() {
    super.onPause();
    String room = roomEditText.getText().toString();
    String roomListJson = new JSONArray(roomList).toString();
    SharedPreferences.Editor editor = sharedPref.edit();
    editor.putString(keyprefRoom, room);
    editor.putString(keyprefRoomList, roomListJson);
    editor.commit();
  }

  @Override
  public void onResume() {
    super.onResume();
    String room = sharedPref.getString(keyprefRoom, "");
    roomEditText.setText(room);
    roomList = new ArrayList<String>();
    String roomListJson = sharedPref.getString(keyprefRoomList, null);
    if (roomListJson != null) {
      try {
        JSONArray jsonArray = new JSONArray(roomListJson);
        for (int i = 0; i < jsonArray.length(); i++) {
          roomList.add(jsonArray.get(i).toString());
        }
      } catch (JSONException e) {
        Log.e(TAG, "Failed to load room list: " + e.toString());
      }
    }
    adapter = new ArrayAdapter<String>(
        this, android.R.layout.simple_list_item_1, roomList);
    roomListView.setAdapter(adapter);
    if (adapter.getCount() > 0) {
      roomListView.requestFocus();
      roomListView.setItemChecked(0, true);
    }
  }

  @Override
  protected void onActivityResult(
      int requestCode, int resultCode, Intent data) {
    if (requestCode == CONNECTION_REQUEST && commandLineRun) {
      Log.d(TAG, "Return: " + resultCode);
      setResult(resultCode);
      commandLineRun = false;
      finish();
    }
  }

  private final OnClickListener connectListener = new OnClickListener() {
    @Override
    public void onClick(View view) {
      boolean loopback = false;
      if (view.getId() == R.id.connect_loopback_button) {
        loopback = true;
      }

      commandLineRun = false;
        String roomID =Integer.toString((new Random()).nextInt(100000000));
        new editEntry(roomID).execute();
      connectToRoom(roomID, loopback, 0);
    }
  };



    private final OnClickListener answerListener = new OnClickListener() {
        @Override
        public void onClick(View v) {
            boolean loopback = false;

            new getEntry().execute();

         //   String roomID = getID;


        }
    };


    class getEntry extends AsyncTask<String, String, String> {

        final String url_get = "http://52.74.223.209/test/get_info.php";
        final String url_delete = "http://52.74.223.209/test/close_connection.php";
        JSONParser jParser = new JSONParser();
        String id="3";
        boolean loopback=false;

        String roomID;
        protected void onPreExecute() {}


        protected String doInBackground(String... args) {
            int success;

            try {
             // Building Parameters
            List<NameValuePair> params = new ArrayList<NameValuePair>();
            params.add(new BasicNameValuePair("id", id));
            // getting JSON string from URL
            JSONObject json = jParser.makeHttpRequest(url_get, "GET", params);

            Log.d("BLAH", json.toString());
            success = json.getInt("success");

                if (success == 1) {
                    // successfully received product details
                    JSONArray detailObj = json.getJSONArray("details"); // JSON Array

                    // get first product object from JSON Array
                    JSONObject details = detailObj.getJSONObject(0);
                    roomID = details.getString("url");
                    List<NameValuePair> params2 = new ArrayList<NameValuePair>();
                    params2.add(new BasicNameValuePair("id", id));
                    json = jParser.makeHttpRequest(url_delete, "POST", params2);
                    Log.d("BLAH", json.toString());
                }

                Log.d("BLAH             ", roomID);

                } catch (JSONException e) {
                e.printStackTrace();
            }

            return null;
        }

        protected void onPostExecute(String file_url) {
            if(roomID!="null") {
                connectToRoom(roomID, loopback, 0);

            }
                else
            {
                Toast.makeText(getApplicationContext(), "No Call to answer", Toast.LENGTH_LONG).show();
            }

        }


    }
        private void connectToRoom(String roomID, boolean loopback, int runTimeMs) {
    // Get room name (random for loopback).
//    String roomId =Integer.toString((new Random()).nextInt(100000000));
//    if (loopback) {
//      roomId = Integer.toString((new Random()).nextInt(100000000));
//    } else {
//      roomId = getSelectedItem();
//      if (roomId == null) {
//        roomId = roomEditText.getText().toString();
//      }
//    }




            ////                THIS IS WHERE CHANGES ARE TO BE MADE
            ///
            ///                 SET SETTINGS HERE
            ///
            ///
            ///                 TRIAL AND ERROR
            ///
            ///


    String roomUrl = sharedPref.getString(
        keyprefRoomServerUrl,
        getString(R.string.pref_room_server_url_default));

    // Video call enabled flag.
    boolean videoCallEnabled = sharedPref.getBoolean(keyprefVideoCallEnabled,
        Boolean.valueOf(getString(R.string.pref_videocall_default)));

    // Get default codecs.
    String videoCodec = sharedPref.getString(keyprefVideoCodec,
        getString(R.string.pref_videocodec_default));
    String audioCodec = sharedPref.getString(keyprefAudioCodec,
        getString(R.string.pref_audiocodec_default));

    // Check HW codec flag.
    boolean hwCodec = sharedPref.getBoolean(keyprefHwCodecAcceleration,
        Boolean.valueOf(getString(R.string.pref_hwcodec_default)));

    // Get video resolution from settings.
    int videoWidth = 0;
    int videoHeight = 0;
    String resolution = sharedPref.getString(keyprefResolution,
        getString(R.string.pref_resolution_default));
    String[] dimensions = resolution.split("[ x]+");
    if (dimensions.length == 2) {
      try {
        videoWidth = Integer.parseInt(dimensions[0]);
        videoHeight = Integer.parseInt(dimensions[1]);
      } catch (NumberFormatException e) {
        videoWidth = 0;
        videoHeight = 0;
        Log.e(TAG, "Wrong video resolution setting: " + resolution);
      }
    }

    // Get camera fps from settings.
    int cameraFps = 0;
    String fps = sharedPref.getString(keyprefFps,
        getString(R.string.pref_fps_default));
    String[] fpsValues = fps.split("[ x]+");
    if (fpsValues.length == 2) {
      try {
        cameraFps = Integer.parseInt(fpsValues[0]);
      } catch (NumberFormatException e) {
        Log.e(TAG, "Wrong camera fps setting: " + fps);
      }
    }

    // Get video and audio start bitrate.
    int videoStartBitrate = 0;
    String bitrateTypeDefault = getString(
        R.string.pref_startvideobitrate_default);
    String bitrateType = sharedPref.getString(
        keyprefVideoBitrateType, bitrateTypeDefault);
    if (!bitrateType.equals(bitrateTypeDefault)) {
      String bitrateValue = sharedPref.getString(keyprefVideoBitrateValue,
          getString(R.string.pref_startvideobitratevalue_default));
      videoStartBitrate = Integer.parseInt(bitrateValue);
    }
    int audioStartBitrate = 0;
    bitrateTypeDefault = getString(R.string.pref_startaudiobitrate_default);
    bitrateType = sharedPref.getString(
        keyprefAudioBitrateType, bitrateTypeDefault);
    if (!bitrateType.equals(bitrateTypeDefault)) {
      String bitrateValue = sharedPref.getString(keyprefAudioBitrateValue,
          getString(R.string.pref_startaudiobitratevalue_default));
      audioStartBitrate = Integer.parseInt(bitrateValue);
    }

    // Test if CpuOveruseDetection should be disabled. By default is on.
    boolean cpuOveruseDetection = sharedPref.getBoolean(
        keyprefCpuUsageDetection,
        Boolean.valueOf(
            getString(R.string.pref_cpu_usage_detection_default)));

    // Check statistics display option.
    boolean displayHud = sharedPref.getBoolean(keyprefDisplayHud,
        Boolean.valueOf(getString(R.string.pref_displayhud_default)));

    // Start AppRTCDemo activity.
    Log.d(TAG, "Connecting to room " + roomID + " at URL " + roomUrl);
    if (validateUrl(roomUrl)) {
      Uri uri = Uri.parse(roomUrl);
      Intent intent = new Intent(this, CallActivity.class);
      intent.setData(uri);
      intent.putExtra(CallActivity.EXTRA_ROOMID, roomID);
      intent.putExtra(CallActivity.EXTRA_LOOPBACK, loopback);
      intent.putExtra(CallActivity.EXTRA_VIDEO_CALL, videoCallEnabled);
      intent.putExtra(CallActivity.EXTRA_VIDEO_WIDTH, videoWidth);
      intent.putExtra(CallActivity.EXTRA_VIDEO_HEIGHT, videoHeight);
      intent.putExtra(CallActivity.EXTRA_VIDEO_FPS, cameraFps);
      intent.putExtra(CallActivity.EXTRA_VIDEO_BITRATE, videoStartBitrate);
      intent.putExtra(CallActivity.EXTRA_VIDEOCODEC, videoCodec);
      intent.putExtra(CallActivity.EXTRA_HWCODEC_ENABLED, hwCodec);
      intent.putExtra(CallActivity.EXTRA_AUDIO_BITRATE, audioStartBitrate);
      intent.putExtra(CallActivity.EXTRA_AUDIOCODEC, audioCodec);
      intent.putExtra(CallActivity.EXTRA_CPUOVERUSE_DETECTION,
          cpuOveruseDetection);
      intent.putExtra(CallActivity.EXTRA_DISPLAY_HUD, displayHud);
      intent.putExtra(CallActivity.EXTRA_CMDLINE, commandLineRun);
      intent.putExtra(CallActivity.EXTRA_RUNTIME, runTimeMs);

      startActivityForResult(intent, CONNECTION_REQUEST);
    }
  }









    /////               DO
    ////                RELEVANT
    ///             STUFF
    ///                 BEFORE
    //                  THIS
    //
    //
    //
    //
    //
    //













  private boolean validateUrl(String url) {
    if (URLUtil.isHttpsUrl(url) || URLUtil.isHttpUrl(url)) {
      return true;
    }

    new AlertDialog.Builder(this)
        .setTitle(getText(R.string.invalid_url_title))
        .setMessage(getString(R.string.invalid_url_text, url))
        .setCancelable(false)
        .setNeutralButton(R.string.ok, new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int id) {
              dialog.cancel();
            }
          }).create().show();
    return false;
  }






  private final OnClickListener addRoomListener = new OnClickListener() {
    @Override
    public void onClick(View view) {
      String newRoom = roomEditText.getText().toString();
      if (newRoom.length() > 0 && !roomList.contains(newRoom)) {
        adapter.add(newRoom);
        adapter.notifyDataSetChanged();
      }
///////////////////////////////////






        ////////////////////////////////

    }
  };

    class editEntry extends AsyncTask<String, String, String> {

        final String url_update = "http://52.74.223.209/test/create_entry.php";
        JSONParser jsonParser = new JSONParser();
        String roomID;

        public editEntry(String roomID) {
            this.roomID=roomID;
        }

        protected void onPreExecute()
        {

        }

        protected String doInBackground(String... args) {

            String room = roomID;
            String id = "4";
            String senderID="3";

            Log.d("BLAH          ", room);

            List<NameValuePair> params = new ArrayList<NameValuePair>();
            params.add(new BasicNameValuePair("id", id));
            params.add(new BasicNameValuePair("senderID", senderID));
            params.add(new BasicNameValuePair("room", room));

            JSONObject json = jsonParser.makeHttpRequest(url_update,
                    "POST", params);

            Log.d("BLAH",json.toString());
            // check json success tag

            return null;
        }

        protected void onPostExecute(String file_url) {
            // dismiss the dialog once product uupdated
         //   pDialog.dismiss();
        }

    }

        private final OnClickListener removeRoomListener = new OnClickListener() {
    @Override
    public void onClick(View view) {
      String selectedRoom = getSelectedItem();
      if (selectedRoom != null) {
        adapter.remove(selectedRoom);
        adapter.notifyDataSetChanged();
      }
    }
  };

  private String getSelectedItem() {
    int position = AdapterView.INVALID_POSITION;
    if (roomListView.getCheckedItemCount() > 0 && adapter.getCount() > 0) {
      position = roomListView.getCheckedItemPosition();
      if (position >= adapter.getCount()) {
        position = AdapterView.INVALID_POSITION;
      }
    }
    if (position != AdapterView.INVALID_POSITION) {
      return adapter.getItem(position);
    } else {
      return null;
    }
  }

}
