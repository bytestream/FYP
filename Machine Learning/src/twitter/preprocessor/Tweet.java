package twitter.preprocessor;

/**
 * @author Kieran Brahney
 * @version 1.0
 * @package twitter.preprocessor
 * @since 11/02/14 22:10
 */
public class Tweet {

    private String tweet = "";

    private String date = "";

    private String source = "";

    public Tweet(String tweet, String date, String source) {
        this.tweet = tweet;
        this.date = date;
        this.source = source;
    }

    public String getTweet() {
        return tweet;
    }

    public String getDate() {
        return date;
    }

    public String getSource() {
        return source;
    }
}
