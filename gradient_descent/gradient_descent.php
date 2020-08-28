<?php
  run_tests();

  function print_static_header($fh)
  {
    fwrite($fh, "alpha, max_iterations, pct_diff_conv, ") or die("Unable to print static header");
  }
  function print_csv_header($fh)
  {
    fwrite($fh, "Iteration, theta0, theta1, cost_fn, cost_pct_diff\n") or die("Unable to write header to csv file"); 
  }

  function print_iteration_values($fh, $col_padding, $iteration, $theta0, $theta1, $cost, $cost_pct_diff)
  {
    fwrite($fh, $col_padding." ".$iteration.",".$theta0.",".$theta1.",".$cost.",".$cost_pct_diff."\n") or die("Unable to write iteration values to file");
  } 

  function hypothesis($theta0, $theta1, $x)
  {
    return $theta0 + $theta1 * $x;
  }

  function cost($theta0, $theta1, $x_vals, $y_vals)
  {
    $num_experiments = count($y_vals); 
    $sum = 0;
    for ($j = 0; $j < $num_experiments; ++$j)
    {
      $sum += pow(hypothesis($theta0, $theta1, $x_vals[$j]) - $y_vals[$j], 2);
    }
    
    return $sum; 
  }

  function is_converged_percent($cost_old, $cost, $pct_diff_conv)
  {
    $pct_diff = 100 * ($cost - $cost_old) / $cost_old; 
    echo "cost_old: $cost_old, cost: $cost, pct_diff: $pct_diff<br>";
    return abs($pct_diff) <= $pct_diff_conv; 
  }

  function test_is_converged_percent($pct_diff_conv, $cost_old, $cost, $converged_exp)
  {
    $converged_act = is_converged_percent($cost_old, $cost, $pct_diff_conv);
    report_test_results("is_converged_percent", $converged_act, $converged_exp);
  }

  function test_is_converged_percent_false()
  {
    $pct_diff_conv = 0.1;
    $cost_old = 25;
    $cost = 25.2;
    $converged_exp = FALSE;
    test_is_converged_percent($pct_diff_conv, $cost_old, $cost, $converged_exp);
  }

  function test_is_converged_percent_true()
  {
    $pct_diff_conv = 0.1;
    $cost_old = 25;
    $cost = 25.02;
    $converged_exp = TRUE;
    test_is_converged_percent($pct_diff_conv, $cost_old, $cost, $converged_exp);
  }

  function dcost_dtheta0($theta0, $theta1, $x_vals, $y_vals)
  {
    $num_experiments = count($y_vals);
    echo "dcost_dtheta0 num_experiments: $num_experiments<br>";
    $sum = 0; 
    for ($j = 0; $j < $num_experiments; ++$j)
    {
      $sum += hypothesis($theta0, $theta1, $x_vals[$j]);
    }
    
    return $sum / $num_experiments;
  }

  function test_dcost_dtheta0()
  {
  }

  function dcost_dtheta1($theta0, $theta1, $x_vals, $y_vals)
  {
    $num_experiments = count($y_vals);
    echo "dcost_dtheta1 num_experiments: $num_experiments<br>";
    $sum = 0;
    for ($j = 0; $j < $num_experiments; ++$j)
    {
      $sum += (hypothesis($theta0, $theta1, $x_vals[$j]) - $y_vals[$j]) * $x_vals[$j];
    }
    return $sum / $num_experiments;
  }

  function test_dcost_dtheta1()
  {
  }

  function gradient_descent($x_vals, $y_vals, $alpha)
  {
    $debug_filename = "./files/gradient_descent.csv";
    $fh = fopen($debug_filename, 'w') or die("Failed to create gradient descent debug file");
    $theta0_i = 0;
    $theta1_i = 0;
    $theta0_new = 0;
    $theta1_new = 0;
    $max_iterations = 10000;
//    $max_iterations = 4;
//    $pct_diff_conv = 0.01;
//    $pct_diff_conv = 0.001;
    $pct_diff_conv = 0.0001;

    print_static_header($fh);
    print_csv_header($fh);

    fwrite($fh, $alpha.",".$max_iterations.",".$pct_diff_conv.",");

    $cost = cost($theta0_i, $theta1_i, $x_vals, $y_vals);
    $j = 0;
    print_iteration_values($fh, "", $j, $theta0_new, $theta1_new, $cost, 0);
    
    for ($j = 0; $j < $max_iterations; ++$j)
    {
      $theta0_new = $theta0_i - $alpha * dcost_dtheta0($theta0_i, $theta1_i, $x_vals, $y_vals);
      $theta1_new = $theta1_i - $alpha * dcost_dtheta1($theta0_i, $theta1_i, $x_vals, $y_vals);
    
      echo "t0old: $theta0_i, t0: $theta0_new<br>";
      echo "t1old: $theta1_i, t1: $theta1_new<br>";
 
      $cost_old = cost($theta0_i, $theta1_i, $x_vals, $y_vals);
      $cost = cost($theta0_new, $theta1_new, $x_vals, $y_vals); 
      if (is_converged_percent($cost_old, $cost, $pct_diff_conv))
      {
        echo "Converged!<br>";
        break;
      }
      else
      {
        echo "Not converged. Waiting for $pct_diff_conv pct<br>";
      }

      if ($max_iterations - 1 === $j)
      {
        echo "Failed to converge in $max_iterations iterations<br>";
      }

      // check iteration number, and whether this will print the 1st value
      print_iteration_values($fh, ",,,", $j, $theta0_new, $theta1_new, $cost, 0);
      $theta0_i = $theta0_new;
      $theta1_i = $theta1_new; 
    } 
    $thetas = array($theta0_new, $theta1_new);

    fclose($fh);
    return $thetas;
  }

  function test_gradient_descent()
  {
    $x_vals = array(1, 2, 3);
    $y_vals = array(2.5, 7.5, 11);
    $alpha = 0.0001;
    $theta_exp = array(0, 0);
    $theta_act = gradient_descent($x_vals, $y_vals, $alpha);
    report_test_results("gradient_descent_theta0", $theta_act[0], $theta_exp[0]);
    report_test_results("gradient_descent_theta1", $theta_act[1], $theta_exp[1]);  
  }

  function report_test_results($func_name, $act, $exp)
  {
    echo "$func_name test: output_exp: $exp, output_act: $act<br>";
  }

  function test_hypothesis()
  {
    $theta0 = 2;
    $theta1 = 3;
    $x = 4;
    $y_exp = 14;

    $y_act = hypothesis($theta0, $theta1, $x);
    report_test_results("hypothesis", $y_act, $y_exp);
  }

  function test_cost()
  {
    $theta0 = 2;
    $theta1 = 3;
    $x_vals = array(1, 2, 3);
    $y_vals = array(2.5, 7.5, 11);
    $cost_exp = 6.5;
    $cost_act = cost($theta0, $theta1, $x_vals, $y_vals);
    
    report_test_results("cost", $cost_act, $cost_exp);
  }

  function run_tests()
  {
    test_hypothesis();
    test_cost();
    test_is_converged_percent_false();
    test_is_converged_percent_true();
    test_gradient_descent();
  }
?>
