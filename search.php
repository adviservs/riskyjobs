<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="utf-8" />
    <title>Risky Jobs - Search</title>
    <link rel="stylesheet" href="style.css" />
  </head>

  <body>
    <img src="riskyjobs_title.gif" alt="Risky Jobs" />
    <img src="riskyjobs_fireman.jpg" alt="Risky Jobs" style="float:right" />
    <h3>Risky Jobs - Search Results</h3>

    <?php

      // Эта функция создает строку поискового запроса, используя для этого критерии поиска и вид сортировки
      function build_query($user_search, $sort) {
        $search_query = "SELECT * FROM riskyjobs";
        // Изменения критерия поиска в массиве
        $clean_search = str_replace(',', ' ', $user_search);
        $search_words = explode(' ', $clean_search);
        $final_search_words = array();

        if (count($search_words) > 0) {
          foreach ($search_words as $word) {
            if (!empty($word)) {
              $final_search_words[] = $word;
            }
          }
        }

        $where_list = array();
        // Создание условного выражения с использованием всех критериев поиска
        if (count($final_search_words) > 0) {
          foreach ($final_search_words as $word) {
            $where_list[] = "description LIKE '%$word%'";
          }
        }
        
        $where_clauce = implode(' OR ', $where_list);

        // Добавление к запросу условного выражения WНERE
        if (!empty($where_clauce)) {
          $search_query .= " WHERE $where_clauce";
        }

        // Добавление к запросу выражения, определяюшего порядок сортировки
        // Sort the search query using the sort setting
        switch ($sort) {
          // Ascending by job title
          case 1:
            $search_query .= " ORDER BY title";
            break;
          // Descending by job title
          case 2:
            $search_query .= " ORDER BY title DESC";
            break;
          // Ascending by state
          case 3:
            $search_query .= " ORDER BY state";
            break;
          // Descending by state
          case 4:
            $search_query .= " ORDER BY state DESC";
            break;
          // Ascending by date posted (oldest first)
          case 5:
            $search_query .= " ORDER BY date_posted";
            break;
          // Descending by date posted (newest first)
          case 6:
            $search_query .= " ORDER BY date_posted DESC";
            break;
          default:
            // No sort setting provided, so don't sort the query
        }

        return $search_query;
      }

      // Эта Функция создает заголовки таблицы результатов поиска в виде гиперссылок,
      // щелкая кнопкой мыши по которым пользователь задает вид сортировки результатов поиска
      function generate_sort_links($user_search, $sort) {
        $sort_links = '';

        switch ($sort) {
        case 1:
          $sort_links .= '<td><a href = "' . $_SERVER['PHP_SELF'] . '?usersearch=' . $user_search . '&sort=2">Job Title</a></td><td>Description</td>';
          $sort_links .= '<td><a href = "' . $_SERVER['PHP_SELF'] . '?usersearch=' . $user_search . '&sort=3">State</a></td>';
          $sort_links .= '<td><a href = "' . $_SERVER['PHP_SELF'] . '?usersearch=' . $user_search . '&sort=5">Date Posted</a></td>';
          break;
        case 3:
          $sort_links .= '<td><a href = "' . $_SERVER['PHP_SELF'] . '?usersearch=' . $user_search . '&sort=1">Job Title</a></td><td>Description</td>';
          $sort_links .= '<td><a href = "' . $_SERVER['PHP_SELF'] . '?usersearch=' . $user_search . '&sort=4">State</a></td>';
          $sort_links .= '<td><a href = "' . $_SERVER['PHP_SELF'] . '?usersearch=' . $user_search . '&sort=5">Date Posted</a></td>';
          break;
        case 5:
          $sort_links .= '<td><a href = "' . $_SERVER['PHP_SELF'] . '?usersearch=' . $user_search . '&sort=1">Job Title</a></td><td>Description</td>';
          $sort_links .= '<td><a href = "' . $_SERVER['PHP_SELF'] . '?usersearch=' . $user_search . '&sort=3">State</a></td>';
          $sort_links .= '<td><a href = "' . $_SERVER['PHP_SELF'] . '?usersearch=' . $user_search . '&sort=6">Date Posted</a></td>';
          break;
        default:
          $sort_links .= '<td><a href = "' . $_SERVER['PHP_SELF'] . '?usersearch=' . $user_search . '&sort=1">Job Title</a></td><td>Description</td>';
          $sort_links .= '<td><a href = "' . $_SERVER['PHP_SELF'] . '?usersearch=' . $user_search . '&sort=3">State</a></td>';
          $sort_links .= '<td><a href = "' . $_SERVER['PHP_SELF'] . '?usersearch=' . $user_search . '&sort=5">Date Posted</a></td>';
        }

        return $sort_links;
      }

      // Эта функция создает навигационные гиперссылки на странице результатов поиска,
      // основываясь на значениях номера текущей страницы и общего количества страниц
      function generate_page_links($user_search, $cur_page, $sort, $num_pages) {
        $page_links = '';
        
        // Если это не первая страница - создание гиперссылки «предыдущая страница» (<<)
        if ($cur_page > 1) {
          $page_links .= '<a href="' . $_SERVER['PHP_SELF'] . '?usersearch=' . $user_search . '&sort=' . $sort . '&page=' . ($cur_page - 1) . '"><-</a> ';
        }
        else {
          $page_links .= '<- ';
        }
        
        // Прохождение в цикле всех страниц и создание гиперссылок,
        // указывающих на конкретные страницы
        for ($i = 1; $i <= $num_pages; $i++) {
          
          if ($cur_page == $i) {
            $page_links .= ' ' . $i;
          }
          else {
            $page_links .= '<a href="' . $_SERVER['PHP_SELF'] . '?usersearch=' . $user_search . '&sort=' . $sort . '&page=' . $i . '"> '  . $i . '</a>';
          }
        }
        
        // Если это не последняя страница - создание гиперссылки «следующая страница» (>>)
        if ($cur_page < $num_pages) {
          $page_links .= ' <a href="' . $_SERVER['PHP_SELF'] . '?usersearch=' . $user_search . '&sort=' . $sort . '&page=' . ($cur_page + 1) . '">-></a>';
        }
        else {
          $page_links .= ' ->';
        }
        
        return $page_links;
      }

      // Извлечение идентификатора типа сортировки и поисковой строки URL с посощью суперглобального массива $_GET
      $sort = $_GET['sort'];
      $user_search = $_GET['usersearch'];

      // Расчет данных, необходимых для разбиения текста результатов поиска на страници
      $cur_page = isset($_GET['page']) ? $_GET['page'] : 1;
      $results_per_page = 5;
      $skip = (($cur_page - 1) * $results_per_page);

      // Создание таблицы с результатами поиска
      // Start generating the table of results
      echo '<table border="0" cellpadding="2">';
      
      // Вывод заголовков таблицы результатов поиска
      // Generate the search result headings
      echo '<tr class="heading">';
      echo generate_sort_links($user_search, $sort);
      echo '</tr>';

      // Connect to the database
      require_once('connectvars.php');
      $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

      // Выполнение запроса для извлечения всех записей
      // Query to get the results
      $query = build_query($user_search, $sort);
      $result = mysqli_query($dbc, $query);
      $total = mysqli_num_rows($result);
      $num_pages = ceil($total / $results_per_page);

      // Выполнение запроса для извлечения записей для одной страницы
      $query = $query . " LIMIT $skip, $results_per_page";
      $result = mysqli_query($dbc, $query);
      while ($row = mysqli_fetch_array($result)) {
        echo '<tr class="results">';
        echo '<td valign="top" width="20%">' . $row['title'] . '</td>';
        echo '<td valign="top" width="50%">' . substr($row['description'], 0, 100) . '...</td>';
        echo '<td valign="top" width="10%">' . $row['state'] . '</td>';
        echo '<td valign="top" width="20%">' . substr($row['date_posted'], 0, 10) . '</td>';
        echo '</tr>';
      } 
      echo '</table>';

      // Если вся информация не помещается на одной странице - создание навигационных гиперссьmок
      if ($num_pages > 1) {
        echo generate_page_links($user_search, $cur_page, $sort, $num_pages);
      }

      mysqli_close($dbc);
    ?>

  </body>
</html>
