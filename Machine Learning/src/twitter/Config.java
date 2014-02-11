package twitter;

/**
 * @author Kieran Brahney
 * @version 1.0
 * @package twitter.database
 * @since 11/02/14 13:00
 */
public class Config {

    /**
     * Name of the driver to use
     */
    private static final String JDBC_DRIVER = "com.mysql.jdbc.Driver";

    /**
     * jdbc:mysql://HOST/DATABASE_NAME
     */
    private static final String DB_URL = "jdbc:mysql://87.76.31.107/findchri_twitter";

    /**
     * User with write permissions to DB
     */
    private static final String USER = "findchri_twitter";

    /**
     * Password to authenticate the above user
     */
    private static final String PASS = "*-H4^1b4*$P9|[h";

    public static String getJDBC_DRIVER() {
        return JDBC_DRIVER;
    }

    public static String getDB_URL() {
        return DB_URL;
    }

    public static String getUSER() {
        return USER;
    }

    public static String getPASS() {
        return PASS;
    }

}
