package twitter.preprocessor;

import twitter.Main;

import java.sql.ResultSet;
import java.sql.ResultSetMetaData;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.logging.Logger;

/**
 * @author Kieran Brahney
 * @version 1.0
 * @package twitter.preprocessor
 * @since 11/02/14 22:06
 */
public class User {

    private long user_id = 0;

    private String screen_name = "";

    private int total_followers = 0;

    private int total_friends = 0;

    private ArrayList<Tweet> tweets = new ArrayList<Tweet>();

    private String verdict = "";

    public User(long user_id, String screen_name, int total_followers, int total_friends, String verdict) {
        this.user_id = user_id;
        this.screen_name = screen_name;
        this.total_followers = total_followers;
        this.total_friends = total_friends;
        this.verdict = verdict;
    }

    public ArrayList<Tweet> setTweets() {
        try {
            Statement stmt = Main.getDb().getConnection().createStatement();
            ResultSet rs = stmt.executeQuery(
                    "SELECT `tweet`, `creation_date`, `source` " +
                    "FROM `tweets` " +
                    "WHERE `user_id` = '" + getUser_id() + "'"
            );
            ResultSetMetaData rsmd = rs.getMetaData();

            while (rs.next()) {
                Tweet tweet = new Tweet(rs.getString(1), rs.getString(2), rs.getString(3));
                tweets.add(tweet);
            }
        } catch (SQLException e) {
            Logger.getLogger(Logger.GLOBAL_LOGGER_NAME)
                    .warning(e.getMessage());
        }

        return tweets;
    }

    @Override
    public String toString() {
        return(
            "User_ID: " + this.getUser_id() + " | " +
            "@" + this.getScreen_name()  + " | " +
            "Total_Followers: " + this.getTotal_followers() + " | " +
            "Total_Friends: " + this.getTotal_friends() + " | " +
            "Total_Tweets: " + this.tweets.size()
        );
    }

    public long getUser_id() {
        return user_id;
    }

    public String getScreen_name() {
        return screen_name;
    }

    public int getTotal_followers() {
        return total_followers;
    }

    public int getTotal_friends() {
        return total_friends;
    }
}
