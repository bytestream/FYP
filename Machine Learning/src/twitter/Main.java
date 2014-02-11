package twitter;

import twitter.database.DatabaseHandler;
import twitter.preprocessor.Preprocessor;

import java.util.logging.Logger;

/**
 * @author Kieran Brahney
 * @version 1.0
 * @package twitter
 * @since 11/02/14 13:19
 */
public class Main {

    private final static Logger logger = Logger.getLogger(Main.class.getName());

    private static DatabaseHandler db;

    public static void main(String[] args) {
        // Initialise connection to database
        db = new DatabaseHandler();

        Preprocessor preprocessor = new Preprocessor();
        System.out.println(preprocessor.getUsers());
    }

    public static DatabaseHandler getDb() {
        return db;
    }

}
