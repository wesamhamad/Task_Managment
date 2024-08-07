import 'package:flutter/material.dart';

class AppState extends ChangeNotifier {
  int selectedCategory = 0;
  void updateCategory(int selectedCatgory) {
    this.selectedCategory = selectedCatgory;
    notifyListeners();
  }
}
