<?php
  run_tests();

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
    return abs($pct_diff) <= $pct_diff_conv; 
  }

  function dcost_dtheta0($theta0, $theta1, $x_vals, $y_vals)
  {
    $num_experiments = count($y_vals);
    return cost($theta0, $theta1, $x_vals, $y_vals) / $num_experiments;
  }

  function dcost_dtheta1($theta0, $theta1, $x_vals, $y_vals)
  {
    $num_experiments = count($y_vals);
    return cost($theta0, $theta1, $x_vals, $y_vals) * $theta1 / $num_experiments;
  }

  function gradient_descent($x_vals, $y_vals, $alpha)
  {
    $theta0_i = 0;
    $theta1_i = 0;
    $theta0_new = 0;
    $theta1_new = 0;
    $max_iterations = 1000;
    for ($j = 0; $j < $max_iterations; ++$j)
    {
      $theta0_new = $theta0_i - $alpha * dcost_dtheta0($theta0_i, $theta1_i, $x_vals, $y_vals);
      $theta1_new = $theta1_i - $alpha * dcost_dtheta1($theta0_i, $theta1_i, $x_vals, $y_vals);
     
      $cost_old = cost($theta0_i, $theta1_i, $x_vals, $y_vals);
      $cost = cost($theta0_new, $theta1_new, $x_vals, $y_vals); 
      if (is_converged_percent($cost_old, $cost, $pct_diff_conv)
      {
        break;
      }
      if ($j = $max_iterations - 1)
      {
        echo "Failed to converge in $max_iterations iterations";
      }
      $theta0_i = $theta0_new;
      $theta1_i = $theta1_new; 
    } 
    $thetas = array($theta0_new, $theta1_new);
    return $thetas;
  }

  function test_gradient_descent()
  {
    $x_vals = array();
    $y_vals = array();
    $alpha = 0.001;
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
//    echo "Hypothesis test: y_exp: $y_exp, y_act: $y_act";
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
    test_gradient_descent();
  }
?>
